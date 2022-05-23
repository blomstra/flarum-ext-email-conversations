import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import sortTags from 'flarum/tags/utils/sortTags';
import Alert from 'flarum/common/components/Alert';
import LinkButton from 'flarum/common/components/LinkButton';

import type Mithril from 'mithril';
import ItemList from 'flarum/common/utils/ItemList';

export default class SettingsPage extends ExtensionPage {
  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    this.refresh();
  }

  refresh() {
    app.store.find('tags', { include: 'parent' }).then(() => {
      m.redraw();
    });
  }

  isMailgunConfigured() {
    //TODO add more checks here
    return this.setting('mail_driver')() !== 'mailgun';
  }

  errorControls() {
    const items = new ItemList<Mithril.Children>();

    items.add(
      'mailSettings',
      <LinkButton className="Button Button--link" href={'/mail'}>
        {app.translator.trans('blomstra-email-conversations.admin.settings.alert.mail_settings')}
      </LinkButton>,
      50
    );

    return items;
  }

  content() {
    const mailgunReady = this.isMailgunConfigured();

    const tags = sortTags(app.store.all('tags'))
      .filter((t) => t.position() !== null)
      .reduce(
        (acc, curr) => {
          acc[curr.slug()] = curr.name() + ' (' + curr.slug() + ')';
          return acc;
        },
        { '': app.translator.trans('blomstra-email-conversations.admin.settings.email_post_tag_none') }
      );

    return (
      <div className="EmailConversationsSettingsPage">
        {mailgunReady && (
          <div className="EmailConversationsSettingsPage--alert">
            <Alert type="error" dismissible={false} controls={this.errorControls().toArray()}>
              {app.translator.trans('blomstra-email-conversations.admin.settings.alert.mailgun_not_configured')}
            </Alert>
          </div>
        )}
        <div className="container">
          <div className="EmailConversations-content">
            {this.buildSettingComponent({
              setting: 'blomstra-email-conversations.max-additional-emails-count',
              type: 'number',
              min: 0,
              max: 100,
              label: app.translator.trans('blomstra-email-conversations.admin.settings.maximum_additional_emails_per_user'),
              help: app.translator.trans('blomstra-email-conversations.admin.settings.maximum_additional_emails_per_user_help'),
              placeholder: '5',
            })}

            {this.buildSettingComponent({
              label: app.translator.trans('blomstra-email-conversations.admin.settings.email_post_tag'),
              help: app.translator.trans('blomstra-email-conversations.admin.settings.email_post_tag_help'),
              type: 'select',
              setting: 'blomstra-email-conversations.tag-slug',
              options: tags,
              default: '',
            })}
            {this.buildSettingComponent({
              setting: 'blomstra-email-conversations.auto-subscribe',
              type: 'bool',
              label: 'Auto subscribe to discussions when posted via email',
              help: 'Auto subscribe to discussions when posted via email',
            })}
            {this.buildSettingComponent({
              setting: 'blomstra-email-conversations.require_approval',
              type: 'bool',
              label: 'Require approval of new discussions started via email',
              help: 'Require approval of new discussions started via email',
            })}
          </div>
          {this.submitButton()}
        </div>
      </div>
    );
  }
}
