import app from 'flarum/admin/app';

app.initializers.add('blomstra/post-by-mail', () => {
  app.extensionData.for('blomstra-post-by-mail')
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
