import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';

export default function addAdditionalEmailsAttributeToUser() {
  User.prototype.additionalEmails = Model.hasMany('additional_emails');
}
