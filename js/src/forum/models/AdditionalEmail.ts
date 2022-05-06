import Model from 'flarum/common/Model';

export default class AdditionalEmail extends Model {
    userId = Model.attribute<number>('user_id');
    email = Model.attribute<string>('email');
    isConfirmed = Model.attribute<boolean>('is_confirmed');
}
