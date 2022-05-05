import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';

import SettingsPage from 'flarum/forum/components/SettingsPage';
import ItemList from 'flarum/common/utils/ItemList';

import EmailDisplay from '../components/MultiEmailSettings';

import type Mithril from 'mithril';

export default function addMultiEmailToUserSettings() {
  extend(SettingsPage.prototype, 'accountItems', function (items: ItemList<Mithril.Children>) {
    if (this.user !== app.session.user) {
      return;
    }

    items.add('emailAddresses', <EmailDisplay user={this.user} />, -100);
  });
}
