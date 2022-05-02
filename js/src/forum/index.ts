import app from 'flarum/forum/app';
import addEmailControlsToProfile from './addEmailControlsToProfile';
import AdditionalEmail from './models/AdditionalEmail';

app.initializers.add('blomstra/post-by-mail', () => {
  app.store.models['blomstra-additional-email'] = AdditionalEmail;
  
  addEmailControlsToProfile();
});
