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
        { '': app.translator.trans('blomstra-post-by-mail.settings.email_post_tag_none') }
      );

    return (
      <div className="container">
        <div className="PostByMailSettingsPage">
          <div className="PostByMailSettingsPage-content">
            {this.buildSettingComponent({
              setting: 'blomstra-post-by-mail.max-additional-emails-count',
              type: 'number',
              min: 0,
              max: 100,
              label: app.translator.trans('blomstra-post-by-mail.admin.settings.maximum_additional_emails_per_user'),
              placeholder: '5',
            })}
            {this.buildSettingComponent({
              setting: 'blomstra.post-by-mail.mailgun-private-key',
              type: 'text',
              label: 'Mailgun private api key',
            })}
            {this.buildSettingComponent({
              setting: 'blomstra.post-by-mail.webhook-signing-key',
              type: 'text',
              label: 'Mailgun webhook signing key',
            })}
            {this.buildSettingComponent({
              label: app.translator.trans('blomstra-post-by-mail.settings.email_post_tag'),
              help: app.translator.trans('blomstra-post-by-mail.settings.gemail_post_tag_help'),
              type: 'select',
              setting: 'blomstra-post-by-mail.tag-slug',
              options: tags,
              default: '',
            })}
          </div>
          {this.submitButton()}
        </div>
      </div>
    );
  }
}
