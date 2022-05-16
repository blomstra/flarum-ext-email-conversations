import app from 'flarum/forum/app';
import Component, { ComponentAttrs } from 'flarum/common/Component';
import classList from 'flarum/common/utils/classList';
import icon from 'flarum/common/helpers/icon';
import Button from 'flarum/common/components/Button';

import redactEmailAddress from '../../redactEmailAddress';

import type Mithril from 'mithril';

interface IAttrs {
  email: string;
  className?: string;
  class?: string;
  unconfirmedIcon?: boolean;
  onDelete?: () => void;
}

interface IState {
  isEmailRedacted: boolean;
}

export default class EmailAddress extends Component<IAttrs, IState> {
  state = {
    isEmailRedacted: true,
  };

  view(vnode: Mithril.Vnode<ComponentAttrs, this>) {
    const { email, class: _class, className, onDelete, unconfirmedIcon = false } = this.attrs;

    return (
      <div class={classList('MultiEmailSettings-Email', _class, className)}>
        {unconfirmedIcon && (
          <span class="MultiEmailSettings-EmailUnconfirmedChip">
            {icon('fa-fw fas fa-exclamation')} {this.trans('unconfirmed_warning')}
          </span>
        )}

        <span class="MultiEmailSettings-EmailAddress">{this.state.isEmailRedacted ? redactEmailAddress(email) : email}</span>
        <Button
          class="Button Button--icon"
          onclick={this.toggleRedacted.bind(this)}
          icon={this.state.isEmailRedacted ? 'fas fa-eye-slash' : 'fas fa-eye'}
          aria-label={this.trans(this.state.isEmailRedacted ? 'show-email' : 'hide-email')}
        />
        {!!onDelete && <Button class="Button Button--icon" onclick={() => onDelete()} icon="fas fa-times" aria-label={this.trans('remove-email')} />}
      </div>
    );
  }

  toggleRedacted() {
    this.state.isEmailRedacted = !this.state.isEmailRedacted;
    m.redraw();
  }

  trans(key: string, vals?: Record<string, unknown>) {
    return app.translator.trans(`blomstra-email-conversations.forum.profile.settings.multi-email.email.${key}`, vals);
  }
}
