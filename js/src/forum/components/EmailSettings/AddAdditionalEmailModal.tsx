import app from 'flarum/forum/app';

import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';

import type User from 'flarum/common/models/User';
import Button from 'flarum/common/components/Button';

interface IAttrs extends IInternalModalAttrs {
  user: User;
  onSave: () => void;
}

export default class AddAdditionalEmailModal extends Modal<IAttrs> {
  loading = false;

  className(): string {
    return 'AddAdditionalEmailModal';
  }

  title() {
    return this.trans('title');
  }

  content() {
    return (
      <div class="Modal-body">
        <div className="Form-group">
          <label for="AddAdditionalEmailModal-input">{this.trans('email_label')}</label>
          <input id="AddAdditionalEmailModal-input" disabled={this.loading} type="email" class="FormControl" />
        </div>

        <Button disabled={this.loading} loading={this.loading} class="Button Button--primary" onclick={() => this.saveEmail()}>
          {this.trans('add_button')}
        </Button>
      </div>
    );
  }

  onsubmit(e: SubmitEvent): void {
    e.preventDefault();
    this.saveEmail();
  }

  async saveEmail() {
    if (this.loading) return;

    this.loading = true;
    m.redraw();

    const email = app.store.createRecord('blomstra-additional-email');

    try {
      await email.save({
        email: this.$('input[type="email"]').val(),
        relationships: { user: this.attrs.user },
      });
      this.hide();
    } catch {
      this.loading = false;
    }

    m.redraw();
  }

  trans(key: string, vals?: Record<string, unknown>) {
    return app.translator.trans(`blomstra-post-by-mail.forum.profile.settings.multi-email.add-additional-email-modal.${key}`, vals);
  }
}
