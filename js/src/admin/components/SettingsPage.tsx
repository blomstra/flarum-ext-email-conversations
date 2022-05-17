import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import sortTags from 'flarum/tags/utils/sortTags';

import type Mithril from 'mithril';

export default class SettingsPage extends ExtensionPage {
  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);

    app.store.find('tags', { include: 'parent' }).then(() => {
      m.redraw();
    });
  }

  content() {
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
      <div className="container">
        <div className="EmailConversationsSettingsPage">
          <div className="EmailConversations-content">
            
            {this.buildSettingComponent({
              setting: 'blomstra-email-conversations.max-additional-emails-count',
              type: 'number',
              min: 0,
              max: 100,
              label: app.translator.trans('blomstra-email-conversations.admin.settings.maximum_additional_emails_per_user'),
              placeholder: '5',
            })}
            {this.buildSettingComponent({
              setting: 'blomstra.email-conversations.mailgun-private-key',
              type: 'text',
              label: 'Mailgun private api key',
            })}
            {this.buildSettingComponent({
              setting: 'blomstra.email-conversations.mailgun.webhook-signing-key',
              type: 'text',
              label: 'Mailgun webhook signing key',
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
              label: 'Auto subscribe to discussions when posted via email'
            })}
            {this.buildSettingComponent({
              setting: 'blomstra-email-conversations.require_approval',
              type: 'bool',
              label: 'Require approval of new discussions started via email'
            })}
          </div>
          {this.submitButton()}
        </div>
      </div>
    );
  }
}
