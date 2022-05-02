import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import SettingsPage from 'flarum/forum/components/SettingsPage';
import type Mithril from 'mithril';
import ItemList from 'flarum/common/utils/ItemList';
import EmailDisplay from './components/EmailDisplay';

export default function addEmailControlsToProfile() {
  extend(SettingsPage.prototype, 'accountItems', function (items: ItemList<Mithril.Children>) {
    if (this.user !== app.session.user) {
      return;
    }

    items.add('emailAddresses', <EmailDisplay user={this.user} />, -100);
  });
}
