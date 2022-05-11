import app from 'flarum/forum/app';

import addMultiEmailToUserSettings from './extend/addMultiEmailToUserSettings';
import AdditionalEmail from './models/AdditionalEmail';
import commonInit from '../common';
import addSourceToCommentPost from './extend/addSourceToCommentPost';

app.initializers.add('blomstra/post-by-mail', () => {
  commonInit();

  app.store.models['blomstra-additional-email'] = AdditionalEmail;

  addMultiEmailToUserSettings();
  addSourceToCommentPost();
});
