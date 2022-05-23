import app from 'flarum/admin/app';
import { extend, override } from 'flarum/common/extend';
import MailPage from 'flarum/admin/components/MailPage';
import ItemList from 'flarum/common/utils/ItemList';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Alert from 'flarum/common/components/Alert';
import FieldSet from 'flarum/common/components/FieldSet';

import Mithril from 'mithril';
import Button from 'flarum/common/components/Button';

export default function extendMailPage() {
  //add an mailItems() function to MailPage, until PR is merged
  MailPage.prototype.mailItems = function () {
    const items = new ItemList();
    const fields = this.driverFields[this.setting('mail_driver')()];
    const fieldKeys = Object.keys(fields);

    items.add(
      'mail-from',
      this.buildSettingComponent({
        type: 'text',
        setting: 'mail_from',
        label: app.translator.trans('core.admin.email.addresses_heading'),
      }),
      100
    );

    items.add(
      'mail-driver',
      this.buildSettingComponent({
        type: 'select',
        setting: 'mail_driver',
        options: Object.keys(this.driverFields).reduce((memo, val) => ({ ...memo, [val]: val }), {}),
        label: app.translator.trans('core.admin.email.driver_heading'),
      }),
      95
    );

    !this.status.sending &&
      items.add('sending-status', <Alert dismissible={false}>{app.translator.trans('core.admin.email.not_sending_message')}</Alert>, 90);

    fieldKeys.length > 0 &&
      items.add(
        'driver-settings',
        <FieldSet label={app.translator.trans(`core.admin.email.${this.setting('mail_driver')()}_heading`)} className="MailPage-MailSettings">
          <div className="MailPage-MailSettings-input">{this.fieldKeyItems(fields, fieldKeys).toArray()}</div>
        </FieldSet>,
        60
      );

    items.add('submit-button', this.submitButton(), 10);

    items.add(
      'send-test',
      <FieldSet label={app.translator.trans('core.admin.email.send_test_mail_heading')} className="MailPage-MailSettings">
        <div className="helpText">{app.translator.trans('core.admin.email.send_test_mail_text', { email: app.session.user.email() })}</div>
        <Button className="Button Button--primary" disabled={this.sendingTest || this.isChanged()} onclick={() => this.sendTestEmail()}>
          {app.translator.trans('core.admin.email.send_test_mail_button')}
        </Button>
      </FieldSet>,
      0
    );

    return items;
  };

  // add fieldKeyItems until PR is merged
  MailPage.prototype.fieldKeyItems = function (fields, fieldKeys) {
    const items = new ItemList();
    var priority = 100;

    fieldKeys.map((field) => {
      const fieldInfo = fields[field];

      items.add(
        'field-' + field,
        <>
          {this.buildSettingComponent({
            type: typeof fieldInfo === 'string' ? 'text' : 'select',
            label: app.translator.trans(`core.admin.email.${field}_label`),
            setting: field,
            options: fieldInfo,
          })}
          {this.status.errors[field] && <p className="ValidationError">{this.status.errors[field]}</p>}
        </>,
        priority
      );

      priority -= 5;
    });

    return items;
  };

  // Alt content() until PR is merged.
  override(MailPage.prototype, 'content', function (original) {
    if (this.loading) {
      return <LoadingIndicator />;
    }

    return <div className="Form">{this.mailItems().toArray()}</div>;
  });

  extend(MailPage.prototype, 'mailItems', function (items: ItemList<Mithril.Children>) {
    if (this.route === undefined) {
      items.add(
        'create-route',
        <Button className="Button" onclick={() => this.configMailgunRoute()} loading={this.configuringRoute}>
          Create Mailgun incoming route
        </Button>,
        5
      );
    } else {
      items.add(
        'route-info',
        <FieldSet label="Incoming Mailgun route ID">
          <div>
            <p>{this.route['description']}</p>
            <code>{this.route['id']}</code>
          </div>
        </FieldSet>,
        5
      );
    }
    return items;
  });

  override(MailPage.prototype, 'refresh', function (original) {
    this.loading = true;

    this.status = { sending: false, errors: {} };

    app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/mail/settings',
      })
      .then((response) => {
        this.driverFields = response['data']['attributes']['fields'];
        this.status.sending = response['data']['attributes']['sending'];
        this.status.errors = response['data']['attributes']['errors'];
        this.route = response['data']['attributes']['route'];

        this.loading = false;
        m.redraw();
      });
  });

  MailPage.prototype.configMailgunRoute = function () {
    this.configuringRoute = true;

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/mailgun/create/route',
        data: {},
      })
      .then(() => {
        this.configuringRoute = false;
        this.refresh();
        m.redraw();
      });
  };
}
