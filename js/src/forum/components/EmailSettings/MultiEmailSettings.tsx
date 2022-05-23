import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import extractText from 'flarum/common/utils/extractText';

import type Mithril from 'mithril';
import type User from 'flarum/common/models/User';

import EmailAddress from './EmailAddress';
import AdditionalEmail from '../../models/AdditionalEmail';
import Button from 'flarum/common/components/Button';
import AddAdditionalEmailModal from './AddAdditionalEmailModal';

interface IAttrs {
  user: User;
}

interface IState {
  loadingAdditional: boolean;
  errorLoadingAdditional: boolean;
}

export default class MultiEmailSettings extends Component<IAttrs, IState> {
  state: IState = {
    loadingAdditional: true,
    errorLoadingAdditional: false,
  };

  oncreate(vnode: Mithril.VnodeDOM<IAttrs, this>): void {
    super.oncreate(vnode);

    this.loadAdditionalEmails();
  }

  view(vnode: Mithril.Vnode<IAttrs, this>) {
    const user = this.attrs.user;

    const additionalEmails = app.store.all<AdditionalEmail>('blomstra-additional-email');

    return (
      <div class="MultiEmailSettings">
        <fieldset>
          <legend>{this.trans('primary-email')}</legend>
          <p className="helpText">{this.trans('primary-email-help')}</p>

          <EmailAddress email={user.email()} />
        </fieldset>

        <fieldset>
          <legend>{this.trans('additional-email')}</legend>
          <p className="helpText">{this.trans('additional-email-help')}</p>

          {this.state.loadingAdditional && <LoadingIndicator containerClassName="MultiEmailSettings-LoadingAdditional" />}
          {this.state.errorLoadingAdditional && (
            <p className="MultiEmailSettings-AdditionalStatus" role="status">
              {this.trans('error_loading_additional')}
            </p>
          )}

          {!this.state.loadingAdditional && !this.state.errorLoadingAdditional && additionalEmails.length === 0 && (
            <p className="MultiEmailSettings-AdditionalStatus" role="status">
              {this.trans('no_additional_emails')}
            </p>
          )}

          {additionalEmails.length > 0 && (
            <ul class="MultiEmailSettings-AdditionalEmails">
              {additionalEmails.map((email) => (
                <EmailAddress
                  key={email.id()}
                  unconfirmedIcon={!email.isConfirmed()}
                  email={email.email()}
                  onDelete={() => {
                    if (confirm(extractText(this.trans('confirm_delete_additional_email', { email: email.email() })))) {
                      email.delete().then(m.redraw);
                    }
                  }}
                />
              ))}
            </ul>
          )}

          <Button
            class="Button MultiEmailSettings-AddAdditionalEmail"
            icon="fas fa-plus"
            onclick={() => {
              app.modal.show(AddAdditionalEmailModal, { user });
            }}
          >
            {this.trans('add_additional_email')}
          </Button>
        </fieldset>
      </div>
    );
  }

  async loadAdditionalEmails() {
    await app.store.find<AdditionalEmail[]>('blomstra-additional-email', {});
    this.state.loadingAdditional = false;
    m.redraw();
  }

  trans(key: string, vals?: Record<string, unknown>) {
    return app.translator.trans(`blomstra-email-conversations.forum.profile.settings.multi-email.${key}`, vals);
  }
}
