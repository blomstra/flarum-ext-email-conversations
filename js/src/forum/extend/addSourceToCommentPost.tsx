import { extend } from "flarum/common/extend";
import Model from "flarum/common/Model";
import Post from "flarum/common/models/Post";
import ItemList from "flarum/common/utils/ItemList";
import CommentPost from 'flarum/forum/components/CommentPost'

import type Mithril from 'mithril';

export default function addSourceToCommentPost() {
    Post.prototype.source = Model.attribute('source');

    extend(CommentPost.prototype, 'headerItems', function (items: ItemList<any>) {
        items.add('source', <div>{this.attrs.post.source?.()}</div>)
    })
}
