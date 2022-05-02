import Model from 'flarum/common/Model';

export default class AdditionalEmail extends Model {
    userId = Model.attribute('user_id');
    email = Model.attribute('email');
    isConfirmed = Model.attribute('is_confirmed');
}
