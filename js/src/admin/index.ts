import app from 'flarum/admin/app';
import commonInit from '../common';
import SettingsPage from './components/SettingsPage';

app.initializers.add('blomstra/post-by-mail', () => {
  commonInit();

  app.extensionData
    .for('blomstra-post-by-mail')
    .registerPage(SettingsPage)
    .registerPermission(
      {
        icon: 'fas fa-mail-bulk',
        label: app.translator.trans('blomstra-post-by-mail.admin.permissions.have_additional_emails'),
        permission: 'haveAdditionalEmail',
      },
      'start'
    );
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
