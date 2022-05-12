import app from 'flarum/forum/app';
import { extend } from "flarum/common/extend";
import Model from "flarum/common/Model";
import Post from "flarum/common/models/Post";
import ItemList from "flarum/common/utils/ItemList";
import CommentPost from 'flarum/forum/components/CommentPost'
import Tooltip from 'flarum/common/components/Tooltip';

import type Mithril from 'mithril';

export default function addSourceToCommentPost() {
    Post.prototype.source = Model.attribute('source');
    Post.prototype.sourceData = Model.attribute('source_data');

    extend(CommentPost.prototype, 'headerItems', function (items: ItemList<any>) {
        const source = this.attrs.post.source();
        const sourceData = this.attrs.post.sourceData();

        if (!source) {
            return;
        }

        const displayText = app.translator.trans('blomstra-post-by-mail.forum.post.source');
        const className = 'PostedByEmail';

        let element;

        if (source === 'blomstra-post-by-mail' && !sourceData) {
            element = (
                <span className={className}>{displayText}</span>
            )
        } else {
            element = (
                <Tooltip text={sourceData}><span className={className}>{displayText}</span></Tooltip>
            )
        }

        items.add('source', element, 90)
    })
}
