import app from 'flarum/forum/app';
import Component, { ComponentAttrs } from 'flarum/common/Component';
import type Mithril from 'mithril';
import type User from 'flarum/common/models/User';

interface IAttrs {
  user: User;
}

export default class EmailDisplay extends Component<IAttrs> {
  view(vnode: Mithril.Vnode<ComponentAttrs, this>) {
    const user = this.attrs.user;
    return (
      <div class="EmailDisplay">
        <h5>{app.translator.trans('blomstra-post-by-mail.forum.profile.settings.primary-email')}</h5>
        <p className="helpText">{app.translator.trans('blomstra-post-by-mail.forum.profile.settings.primary-email-help')}</p>
        <p>{user.email()}</p>
        <h5>{app.translator.trans('blomstra-post-by-mail.forum.profile.settings.additional-email')}</h5>
        <p className="helpText">{app.translator.trans('blomstra-post-by-mail.forum.profile.settings.additional-email-help')}</p>
      </div>
    );
  }
}
