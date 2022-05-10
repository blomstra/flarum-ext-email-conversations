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
      label: 'Maximum number of additional emails per user',
      placeholder: '5',
    })
    .registerPermission(
      {
        icon: 'fas fa-mail-bulk',
        label: 'have additional email addresses',
        permission: 'setAdditionalEmail',
      },
      'start'
    )
    .registerPermission(
      {
        icon: 'fas fa-mail-bulk',
        label: 'manage other users additional mail addresses',
        permission: 'viewAdditionalEmailAddresses',
      },
      'moderate'
    );
});
