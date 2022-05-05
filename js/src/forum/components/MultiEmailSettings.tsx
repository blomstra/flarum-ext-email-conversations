import app from 'flarum/forum/app';
import Component, { ComponentAttrs } from 'flarum/common/Component';

import type Mithril from 'mithril';
import type User from 'flarum/common/models/User';

interface IAttrs {
  user: User;
}

interface IState {
  additionalEmails: string[];
}

export default class MultiEmailSettings extends Component<IAttrs> {
  view(vnode: Mithril.Vnode<ComponentAttrs, this>) {
    const user = this.attrs.user;
    return (
      <div class="MultiEmailSettings">
        <fieldset>
          <legend>{this.trans('primary-email')}</legend>
          <p className="helpText">{this.trans('primary-email-help')}</p>

          <p>{user.email()}</p>
        </fieldset>

        <fieldset>
          <legend>{this.trans('additional-email')}</legend>
          <p className="helpText">{this.trans('additional-email-help')}</p>
        </fieldset>
      </div>
    );
  }

  trans(key: string, vals?: Record<string, unknown>) {
    return app.translator.trans(`blomstra-post-by-mail.forum.profile.settings.multi-email.${key}`, vals);
  }
}
