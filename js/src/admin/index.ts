import app from 'flarum/admin/app';
import commonInit from '../common';

app.initializers.add('blomstra/post-by-mail', () => {
  commonInit();

  app.extensionData
    .for('blomstra-post-by-mail')
    .registerSetting({
      setting: 'blomstra-post-by-mail.max-additional-emails-count',
      type: 'number',
      min: 0,
      max: 100,
      label: app.translator.trans('blomstra-post-by-mail.admin.settings.maximum_additional_emails_per_user'),
      placeholder: '5',
    })
    .registerPermission(
      {
        icon: 'fas fa-mail-bulk',
        label: app.translator.trans('blomstra-post-by-mail.admin.permissions.have_additional_emails'),
        permission: 'haveAdditionalEmail',
      },
      'start'
    )
    // Not worrying about anything but the current user for now, but this will be required for when we add
    // the ability for Mods, etc to edit other users' email addresses.
    // .registerPermission(
    //   {
    //     icon: 'fas fa-mail-bulk',
    //     label: app.translator.trans('blomstra-post-by-mail.admin.permissions.manage_additional_emails_of_others'),
    //     permission: 'viewAdditionalEmailAddresses',
    //   },
    //   'moderate'
    // );
});
