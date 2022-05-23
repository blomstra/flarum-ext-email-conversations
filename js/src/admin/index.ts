import app from 'flarum/admin/app';
import commonInit from '../common';
import SettingsPage from './components/SettingsPage';
import extendMailPage from './extend/extendMailPage';

app.initializers.add('blomstra/email-conversations', () => {
  commonInit();
  extendMailPage();

  app.extensionData
    .for('blomstra-email-conversations')
    .registerPage(SettingsPage)
    .registerPermission(
      {
        icon: 'fas fa-mail-bulk',
        label: app.translator.trans('blomstra-email-conversations.admin.permissions.have_additional_emails'),
        permission: 'haveAdditionalEmail',
      },
      'start'
    );
  // Not worrying about anything but the current user for now, but this will be required for when we add
  // the ability for Mods, etc to edit other users' email addresses.
  // .registerPermission(
  //   {
  //     icon: 'fas fa-mail-bulk',
  //     label: app.translator.trans('blomstra-conversations-emailadmin.permissions.manage_additional_emails_of_others'),
  //     permission: 'viewAdditionalEmailAddresses',
  //   },
  //   'moderate'
  // );
});
