import app from 'flarum/forum/app';

import addMultiEmailToUserSettings from './extend/addMultiEmailToUserSettings';
import AdditionalEmail from './models/AdditionalEmail';
import commonInit from '../common';

app.initializers.add('blomstra/email-conversations', () => {
  commonInit();

  app.store.models['blomstra-additional-email'] = AdditionalEmail;

  addMultiEmailToUserSettings();
});
