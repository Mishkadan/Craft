(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
        'use strict';
        (function (factory) {

            joms.onStart(function () {
                var Chat = factory(joms.jQuery, joms._, joms.Backbone);
                joms.chat = new Chat();
            });
        })(function ($, _, Backbone) {

            var Notification = require('./notification'),
                HeaderView = require('./header'),
                SidebarView = require('./sidebar'),
                MessagesView = require('./messages'),
                MessageBox = require('./messagebox');

            /**
             * Conversation main class.
             * @class Chat
             */
            function Chat() {
                this.init();
            }

            Chat.prototype = {

                /**
                 * Current user information.
                 */
                me: { id: 0, name: '', avatar: '' },

                /**
                 * Buddy list.
                 */
                buddies: {},

                /**
                 * Conversation list.
                 * @type {object}
                 */
                conversations: {},

                opened: {},

                /**
                 * Active conversation.
                 */
                active: {},

                ping: {},

                ajax_get_chat_by_user: {},

                last_activity: 0,

                show_seen: 0,

                ping_time: 1000,

                /**
                 * Chat initialization.
                 */
                init: function init() {
                    var id = +window.joms_my_id,
                        enableReadStatue = +joms.getData('chat_enablereadstatus');

                    this.render();

                    if (!id) {
                        joms_observer.do_action('chat_user_logout');
                        return false;
                    }

                    if (enableReadStatue) {
                        this.show_seen = 1;
                    }

                    this.me.id = id;
                    joms_observer.do_action('chat_user_login');

                    joms_observer.add_action('chat_sidebar_select', this.conversationOpen, 10, 1, this);
                    joms_observer.add_action('chat_messagebox_send', this.messageSend, 10, 2, this);
                    joms_observer.add_action('chat_message_recall', this.messageRecall, 10, 1, this);
                    joms_observer.add_action('chat_single_conversation_get_by_user', this.singleConversationGetByUser, 1, 1, this);
                    joms_observer.add_action('chat_update_draft_conversation', this.updateDraftConversation, 1, 2, this);
                    joms_observer.add_action('chat_create_draft_conversation', this.createDraftConversation, 1, 0, this);
                    joms_observer.add_action('chat_remove_draft_conversation', this.removeDraftConversation, 1, 0, this);
                    joms_observer.add_action('chat_set_location_hash', this.setLocationHash, 1, 1, this);
                    joms_observer.add_action('chat_leave', this.leaveChat, 2, 1, this);
                    joms_observer.add_action('all_peoples', this.allPeoples, 2, 1, this);
                    joms_observer.add_action('change_background', this.changeBackground, 2, 1, this);
                    joms_observer.add_action('change_ava', this.changeAva, 2, 1, this);
                    joms_observer.add_action('chat_add_people', this.addPeople, 1, 1, this);
                    joms_observer.add_action('chat_buddy_add', this.buddyAdd, 1, 3, this);
                    joms_observer.add_action('chat_get_previous_messages', this.conversationGetPrevMessages, 1, 2, this);
                    joms_observer.add_action('chat_mute', this.muteChat, 2, 1, this);
                    joms_observer.add_action('chat_add_conversions', this.addConversations, 1, 1, this);
                    joms_observer.add_action('chat_mark_all_as_read', this.markAllAsRead, 1, 1, this);
                    joms_observer.add_action('chat_change_active_group_chat_name', this.changeActiveGroupChatName, 1, 2, this);

                    var noFriend = 1,
                        noConversation = 1;

                    this.friendListUpdate().done(function (friends) {
                        if (_.isArray(friends) && friends.length) {
                            noFriend = 0;
                        }
                        this.conversationListUpdate().done(function (data) {
                            var $startScreen = $('.joms-js-page-chat-loading'),
                                $chatScreen = $('.joms-js-page-chat');

                            if (data && $.isPlainObject(data.list) && _.keys(data.list).length) {
                                noConversation = 0;
                            }

                            if (noConversation) {
                                if (noFriend) {
                                    $startScreen.find('.joms-js-loading-no-friend').show();
                                } else {
                                    $startScreen.find('.joms-js-loading-no-conv').show().on('click', function () {
                                        $startScreen.hide();
                                        $chatScreen.show();
                                    });
                                }

                                $startScreen.find('.joms-js-loading').hide();
                                $startScreen.find('.joms-js-loading-empty').show();
                            } else {
                                $startScreen.hide();
                                $chatScreen.show();
                            }

                            // Update ping time when browser is in the background.
                            $(window).on('blur', $.proxy(function () {
                                var pingTime = +joms.getData('chat_pooling_time_inactive');
                                if (pingTime >= 1) {
                                    this.ping_time = pingTime * 1000;
                                }
                            }, this)).on('focus', $.proxy(function () {
                                var pingTime = +joms.getData('chat_pooling_time_active');
                                if (pingTime >= 1) {
                                    this.ping_time = pingTime * 1000;
                                }
                            }, this)).triggerHandler('focus');

                            this.conversationPing();
                        });
                    });
                },

                changeActiveGroupChatName: function changeActiveGroupChatName(name, chat_id) {
                    chat_id = chat_id ? chat_id : this.active.chat_id;
                    var params = {};
                    params.groupname = name;

                    joms.ajax({
                        func: 'chat,ajaxChangeGroupChatName',
                        data: [chat_id, name],
                        callback: $.proxy(function (json) {
                            if (json.success) {
                                joms_observer.do_action('sidebar_change_conversation_name', json.groupname, chat_id);
                                joms_observer.do_action('chat_messages_render', null, '', {}, this.me, new Date().getTime(), 'change_chat_name', params);
                            }
                        }, this)
                    });
                },

                markAllAsRead: function markAllAsRead() {
                    joms.ajax({
                        func: 'chat,ajaxMarkAllAsRead',
                        callback: $.proxy(function () {
                            joms_observer.do_action('chat_all_marked_read');
                            _.each(this.conversations, function (conv) {
                                conv.seen = 1;
                            });
                        }, this)
                    });
                },

                addConversations: function addConversations(list) {
                    for (var key in list) {
                        this.conversations[key] = list[key];
                    }
                },

                render: function render() {
                    // initialize views
                    var header = new HeaderView();
                    var sidebar = new SidebarView();
                    var messages = new MessagesView();
                    var messageBox = new MessageBox();
                    var notification = new Notification();
                },

                muteChat: function muteChat(mute) {
                    this.active.mute = +mute ? 0 : 1;
                    joms.ajax({
                        func: 'chat,ajaxMuteChat',
                        data: [this.active.chat_id, this.active.mute]
                    });
                },

                addPeople: function addPeople(friends) {
                    var ids = [],
                        key;

                    for (key in friends) {
                        this.buddyAdd(friends[key].id, friends[key].name, friends[key].avatar);
                        ids.push(key);
                    }

                    joms.ajax({
                        func: 'chat,ajaxAddPeople',
                        data: [this.active.chat_id, JSON.stringify(ids)],
                        callback: $.proxy(function () {
                            var ids = [this.active.chat_id];
                            this.updateChatList(JSON.stringify(ids));
                        }, this)
                    });
                },

                leaveChat: function leaveChat() {
                    var chat_id = this.active.chat_id;
                    joms.ajax({
                        func: 'chat,ajaxLeaveChat',
                        data: [chat_id]
                    });
                    this.active = {};
                    delete this.conversations['chat_' + chat_id];
                    joms_observer.do_action('chat_remove_window', chat_id);
                    joms_observer.do_action('chat_removemove_notification', chat_id);
                    joms_observer.do_action('chat_empty_message_view');
                },
                // get group chat members
                allPeoples: function allPeoples() {
                    var chat_id = this.active.chat_id;
                    joms.ajax({
                        func: 'chat,ajaxAllPeoples',
                        data: [chat_id],
                        callback: $.proxy(function (json) {
                            this.showGroupChatMembers(json);
                        }, this)

                    });
                },
                // change group chat avatar
                changeAva: function changeAva() {
                    var chat_id = this.active.chat_id;
                    joms.ajax({
                        func: 'chat,ajaxChangeGroupChatAva',
                        data: [chat_id],
                        callback: $.proxy(function (json) {
                            this.changeGroupChatAva(json);
                        }, this)

                    });
                },

                setLocationHash: function setLocationHash(chat_id) {
                    window.location.hash = chat_id;
                },

                getLocationHash: function getLocationHash() {
                    var hash = window.location.hash.replace('#', '');
                    return +hash;
                },

                updateDraftConversation: function updateDraftConversation(name, partner) {
                    if (this.conversations['chat_0'].temp_chat_id) {
                        delete this.conversations['chat_0'].temp_chat_id;
                    }
                    this.conversations['chat_0'].name = name;
                    this.conversations['chat_0'].partner = partner;
                    this.active = this.conversations['chat_0'];
                },

                createDraftConversation: function createDraftConversation() {
                    if (!this.conversations.hasOwnProperty('chat_0')) {
                        var conversation = {
                            chat_id: '0',
                            name: '',
                            partner: [],
                            type: 'new',
                            thumb: '/components/com_community/assets/mood_21.png'
                        };

                        this.conversations['chat_0'] = conversation;
                        joms_observer.do_action('chat_render_draft_conversation', conversation);
                    }
                    joms_observer.do_action('chat_hightlight_active_window', 0);
                    joms_observer.do_action('chat_conversation_open');
                    this.conversationOpen(0);
                },

                removeDraftConversation: function removeDraftConversation() {
                    $('.joms-js--remove-draft').remove();
                    delete this.conversations['chat_0'];
                },

                singleConversationGetByUser: function singleConversationGetByUser(user_id) {
                    return $.Deferred($.proxy(function (defer) {
                        joms_observer.do_action('chat_messages_loading');
                        joms.ajax({
                            func: 'chat,ajaxGetSingleChatByUser',
                            data: [user_id],
                            callback: $.proxy(function (json) {
                                if (json.partner) {
                                    if (!this.buddies.hasOwnProperty(json.partner.id)) {
                                        this.buddyAdd(json.partner.id, json.partner.name, json.partner.avatar);
                                    }
                                    if (json.messages && _.isArray(json.messages)) {
                                        joms_observer.do_action('chat_messages_loaded', json.messages, this.buddies);
                                    } else {
                                        joms_observer.do_action('chat_empty_message_view');
                                    }
                                    this.conversations['chat_0'].name = json.partner.name;
                                    this.conversations['chat_0'].partner = [json.partner.id];
                                    if (json.chat_id) {
                                        this.conversations['chat_0'].temp_chat_id = json.chat_id;
                                        this.doSeen(json.chat_id);
                                        this.setSeen(json.chat_id);
                                    }
                                    this.active = this.conversations['chat_0'];
                                }
                                defer.resolveWith(this, [json]);
                            }, this)
                        });
                    }, this));
                },

                /**
                 * Get list of conversation by current user.
                 * @return {jQuery.Deferred}
                 */
                conversationListUpdate: function conversationListUpdate() {
                    var localState = joms.localStorage.get('chatbar') || {},
                        opened = localState.opened || [];
                    return $.Deferred($.proxy(function (defer) {
                        joms.ajax({
                            func: 'chat,ajaxInitializeChatData',
                            data: ['[]', JSON.stringify(opened)],
                            callback: $.proxy(function (json) {

                                var hash;

                                this.last_activity = json.last_activity ? json.last_activity : 0;

                                // Update buddy list.
                                _.each(json.buddies, function (buddy) {
                                    this.buddySet(buddy);
                                }, this);

                                var me = _.find(json.buddies, function (buddy) {
                                    return buddy.id == this.me.id;
                                }, this);

                                if (me) {
                                    this.me.profile_link = me.profile_link;
                                }

                                // Update conversation listing.
                                if (json.list) {
                                    this.conversations = json.list;
                                    joms_observer.do_action('chat_conversation_render', this.conversations);
                                    hash = this.getLocationHash();
                                    if (hash) {
                                        joms_observer.do_action('chat_open_window_by_chat_id', hash);
                                    } else {
                                        joms_observer.do_action('chat_open_first_window');
                                    }
                                } else {
                                    joms_observer.do_action('chat_noconversation_render', this.conversations);
                                }

                                if (json.opened) {
                                    this.opened = json.opened;
                                }

                                this.updateConversations();

                                defer.resolveWith(this, [json]);
                                joms_observer.do_action('chat_initialized');
                            }, this)
                        });
                    }, this));
                },

                setActiveChat: function setActiveChat(chat_id) {
                    this.active = this.conversations['chat_' + chat_id];
                    this.updateConversations();
                },

                formatData: function formatData(data, buddies) {
                    for (var i in data) {
                        if (data[i].type === 'single') {
                            var partner = data[i].partner;
                            data[i].thumb = buddies[partner].avatar;
                            data[i].name = buddies[partner].name;
                        } else {
                            data[i].thumb = joms.BASE_URL + 'components/com_community/assets/group_thumb.jpg';
                        }
                    }
                    return data;
                },

                /**
                 * Open a conversation with specified ID.
                 * @param {number} [chat_id]
                 * @return {jQuery.Deferred}
                 */
                conversationOpen: function conversationOpen(chat_id) {
                    var isChatView = joms.getData('is_chat_view');

                    // BUG: when send msg from draft conversation
                    if (isChatView) {
                        this.setLocationHash(chat_id);
                    }

                    if (+chat_id === +this.active.chat_id) {
                        return;
                    }

                    if (this.active.temp_chat_id && this.active.temp_chat_id == chat_id) {
                        this.setActiveChat(chat_id);
                        return;
                    }

                    if (+chat_id === 0 && this.conversations['chat_0'].temp_chat_id && this.conversations['chat_0'].temp_chat_id == this.active.chat_id) {
                        this.setActiveChat(0);
                        return;
                    }

                    if (!this.conversations['chat_' + chat_id]) {
                        joms_observer.do_action('chat_open_first_window');
                        return;
                    }

                    this.setActiveChat(chat_id);

                    if (this.active.temp_chat_id) {
                        chat_id = this.active.temp_chat_id;
                    }

                    joms_observer.do_action('chat_conversation_open', this.active.type, this.active.participants);
                    joms_observer.do_action('chat_empty_message_view');

                    var users = this.conversations['chat_' + chat_id].users.join(',');
                    joms_observer.do_action('chat_render_option_dropdown', this.active.type, this.active.mute, users);

                    return $.Deferred($.proxy(function (defer) {
                        if (chat_id) {
                            // get previous messages
                            this.conversationGetPrevMessages(chat_id, 0).done($.proxy(function (json) {
                                if (this.active.type === 'single' && this.active.blocked) {
                                    joms_observer.do_action('chat_disable_message_box');
                                }
                                defer.resolveWith(this);
                            }, this));
                        }
                    }, this));
                },

                /**
                 * Get conversation messages before specific message defined it's ID.
                 * @param {number} chatId
                 * @param {number} [lastMessageId]
                 * @returns jQuery.Deferred
                 */
                conversationGetPrevMessages: function conversationGetPrevMessages(chat_id, offset) {
                    if (this.getting_previous_messagse) {
                        return;
                    }
                    if (chat_id) {
                        joms_observer.do_action('chat_messages_loading');
                    } else {
                        if (+this.active.chat_id) {
                            chat_id = +this.active.chat_id;
                        } else if (this.active.temp_chat_id) {
                            chat_id = +this.active.temp_chat_id;
                        } else {
                            joms_observer.do_action('chat_previous_messages_loaded', []);
                            return;
                        }
                    }
                    this.getting_previous_messagse = 1;
                    return $.Deferred($.proxy(function (defer) {
                        joms.ajax({
                            func: 'chat,ajaxGetLastChat',
                            data: [chat_id, offset],
                            callback: $.proxy(function (json) {
                                this.getting_previous_messagse = 0;
                                if (_.isArray(json.messages) && _.isArray(json.seen)) {
                                    if (offset) {
                                        if (!json.messages.length) {
                                            json.messages.push({
                                                id: 0,
                                                message: null,
                                                attachment: null,
                                                user: null,
                                                timestamp: null,
                                                action: 'end'
                                            });
                                        }
                                        joms_observer.do_action('chat_previous_messages_loaded', json.messages, this.buddies);
                                    } else {
                                        joms_observer.do_action('chat_messages_loaded', json.messages, this.buddies);
                                        if (this.show_seen) {
                                            joms_observer.do_action('chat_seen_message', json.seen, this.me, this.buddies);
                                        }
                                        this.doSeen(chat_id);
                                        this.setSeen(chat_id);
                                    }
                                }
                                defer.resolveWith(this, [json]);
                            }, this)
                        });
                    }, this));
                },

                /**
                 * Ping server for any update on conversations.
                 */
                conversationPing: function conversationPing() {
                    var timestamp;

                    // Cancel scheduled next ping.
                    clearTimeout(this._conversationPingTimer);

                    // Remember timestamp.
                    this._conversationPingTimestamp = timestamp = new Date().getTime();

                    // Perform ping to server.
                    this._conversationPing().done($.proxy(function (json) {
                        var active = [],
                            seen = [],
                            inactive = [],
                            changename = [],
                            data;

                        // Do not proceed if timestamp is different, meaning that ping was called again during request.
                        if (this._conversationPingTimestamp !== timestamp) {
                            return;
                        }

                        // Add new buddy list if any.
                        if (json.newcomer && _.isArray(json.newcomer) && json.newcomer.length) {
                            _.each(json.newcomer, function (buddy) {
                                if (+this.me.id !== +buddy.id) {
                                    this.buddyAdd(buddy.id, buddy.name, buddy.avatar);
                                }
                            }, this);
                        }

                        // Handle received messages.
                        if (json.activities && _.isArray(json.activities) && json.activities.length) {
                            data = json.activities;
                            this.last_activity = data[data.length - 1].id;
                            var self = this;
                            if (joms.store) {
                                _.each(data, function (item) {
                                    var chats = joms.store.state.chats;
                                    if (!chats.info[item.chat_id]) {
                                        return;
                                    }

                                    joms.store.commit('chats/messages/prepend', { messages: [item] });

                                    if (item.action === 'seen') {
                                        joms.store.dispatch('chats/seenBy', { chatid: +item.chat_id, userid: +item.user_id });
                                    } else {
                                        if (self.me.id != item.user_id) {
                                            if (+item.chat_id === +chats.active) {
                                                joms.store.dispatch('chats/seen', +item.chat_id);
                                            } else {
                                                joms.store.dispatch('chats/unread', +item.chat_id);
                                            }

                                            joms.store.dispatch('chats/open', +item.chat_id);
                                            joms.store.dispatch('chats/setTopSidebar', +item.chat_id);
                                        }

                                        joms.store.dispatch('chats/clearSeen', { chatid: +item.chat_id });
                                    }
                                });
                            }

                            changename = _.filter(data, $.proxy(function (item) {
                                return item.action === 'change_chat_name';
                            }, this));

                            _.each(changename, function (item) {
                                var params = JSON.parse(item.params);
                                joms_observer.do_action('sidebar_change_conversation_name', _.escape(params.groupname), item.chat_id);
                                joms.store.dispatch('chats/changeName', { groupname: params.groupname, chat_id: item.chat_id });
                            });

                            if (this.active && _.isObject(this.active)) {
                                active = _.filter(data, $.proxy(function (item) {
                                    var same_chat_id = +this.active.chat_id === +item.chat_id,
                                        temp_chat_active = this.active.temp_chat_id && +this.active.temp_chat_id === +item.chat_id,
                                        is_msg = item.action === 'sent' || item.action === 'leave' || item.action === 'add' || item.action === 'change_chat_name',
                                        not_my_msg = +item.user_id !== +this.me.id;
                                    return is_msg && not_my_msg && (same_chat_id || temp_chat_active);
                                }, this));
                                if (active.length) {
                                    joms_observer.do_action('chat_messages_received', active, this.buddies);
                                    if (+this.active.chat_id) {
                                        this.doSeen(this.active.chat_id);
                                    } else {
                                        this.doSeen(this.active.temp_chat_id);
                                    }
                                    joms_observer.do_action('chat_remove_seen_message');
                                    joms_observer.do_action('chat_move_window_to_top', active);
                                    joms_observer.do_action('chat_move_notification_to_top', active);
                                }


                                var seen = _.filter(data, $.proxy(function (item) {
                                     return item.action === 'seen';
                                 }, this));

                                 if (seen.length) {
                                     for (var i = 0; i < seen.length; i++) {
                                         if (+seen[i].user_id === +this.me.id) {
                                             this.setSeen(seen[i].chat_id);
                                         }
                                     }
                                     var seen_active = _.filter(seen, function (item) {
                                         return +item.chat_id === +this.active.chat_id;
                                     }, this);


                                    /*** console.log(seen_active[0].chat_id); ***/

                                    joms.ajax({ // Craft
                                        func: 'chat,ajaxSetMessagesSeen',
                                        data: [seen_active[0].chat_id],
                                        callback: $.proxy(function (item) {
                                            if (this.show_seen) {
                                                joms_observer.do_action('chat_seen_message', seen_active, this.me, this.buddies);
                                            }
                                        }, this)
                                    });
                                }
                            }

                            inactive = _.filter(data, $.proxy(function (item) {
                                var is_msg = item.action === 'sent',
                                    not_in_active_chat = +this.active.chat_id !== +item.chat_id,
                                    not_new_chat = this.conversations.hasOwnProperty('chat_' + item.chat_id),
                                    not_my_msg = +item.user_id !== +this.me.id;
                                return is_msg && not_my_msg && not_in_active_chat && not_new_chat;
                            }, this));

                            if (inactive.length) {
                                for (var i = 0; i < inactive.length; i++) {
                                    this.setUnread(inactive[i].chat_id);
                                }
                                joms_observer.do_action('chat_highlight_unread_windows', inactive);
                                joms_observer.do_action('chat_move_window_to_top', inactive);
                                joms_observer.do_action('chat_move_notification_to_top', inactive);
                            }

                            // Update sidebar if necessary.
                            var needUpdateSidebar = [];
                            _.each(data, function (item) {
                                var needUpdate = false,
                                    chatId = +item.chat_id;

                                // Exclude my own activities.
                                if (+this.me.id !== +item.user_id) {

                                    // When somebody is leaving or added to the chat.
                                    if (item.action === 'leave' || item.action === 'add') {
                                        needUpdate = true;

                                        // When there is a new conversation.
                                    } else if (!_.has(this.conversations, 'chat_' + chatId) && item.action !== 'seen') {
                                        needUpdate = true;
                                    }
                                } else if (this.me.id == item.user_id && item.action === 'add') {
                                    needUpdate = true;
                                }

                                if (needUpdate && needUpdateSidebar.indexOf(chatId) < 0) {
                                    needUpdateSidebar.push(chatId);
                                }
                            }, this);

                            if (needUpdateSidebar.length) {
                                needUpdateSidebar = JSON.stringify(needUpdateSidebar);
                                this.updateChatList(needUpdateSidebar);
                            }

                            joms_observer.do_action('chat_set_notification_label', this.countUnread(this.conversations));
                        }

                        // Schedule next ping.
                        this._conversationPingTimer = setTimeout($.proxy(function () {
                            this.conversationPing();
                        }, this), this.ping_time);
                    }, this));
                },

                countUnread: function countUnread(data) {
                    var count = 0;
                    for (var key in data) {
                        if (+data[key].seen === 0 && +data[key].mute === 0) {
                            count++;
                        }
                    }
                    return count;
                },

                setUnread: function setUnread(chat_id) {
                    this.conversations['chat_' + chat_id].seen = 0;
                    joms_observer.do_action('chat_set_notification_label_unread', chat_id);
                },

                setSeen: function setSeen(chat_id) {
                    if (this.conversations['chat_' + chat_id]) {
                        this.conversations['chat_' + chat_id].seen = 1;
                        joms_observer.do_action('chat_set_notification_label', this.countUnread(this.conversations));
                        joms_observer.do_action('chat_set_notification_label_seen', chat_id);
                        joms_observer.do_action('chat_set_window_seen', chat_id);
                    }
                },

                doSeen: function doSeen(chat_id) {
                    return $.Deferred($.proxy(function (defer) {
                        joms.ajax({
                            func: 'chat,ajaxSeen',
                            data: [chat_id]
                        });
                    }, this));
                },

                _conversationPing: function _conversationPing() {
                    return $.Deferred($.proxy(function (defer) {
                        this.ping = joms.ajax({
                            func: 'chat,ajaxPingChat',
                            data: [this.last_activity],
                            callback: $.proxy(function (json) {
                                defer.resolveWith(this, [json]);
                            }, this)
                        });
                    }, this));
                },

                /**
                 * Get latest chat listing.
                 * @param {number[]} ids
                 * @return {_.debounce}
                 */
                updateChatList: function updateChatList(ids) {
                    joms.ajax({
                        func: 'chat,ajaxGetChatList',
                        data: [ids],
                        callback: $.proxy(function (json) {
                            var list, unread;

                            _.each(json.buddies, function (buddy) {
                                this.buddySet(buddy);
                            }, this);

                            // Format conversation listing.
                            list = this.formatData(json.list, this.buddies);

                            // Update conversation listing.
                            _.each(list, function (item) {
                                this.conversations['chat_' + item.chat_id] = item;
                            }, this);

                            // Get total unread.
                            unread = this.countUnread(this.conversations);

                            joms_observer.do_action('chat_conversation_render', this.conversations);
                            joms_observer.do_action('chat_highlight_unread_windows', list);
                            joms_observer.do_action('chat_set_notification_label', unread);
                            joms_observer.do_action('chat_move_window_to_top', list);
                            joms_observer.do_action('chat_move_notification_to_top', list);

                            this.updateConversations();

                            if (joms.store) {
                                joms.store.dispatch('chats/fetch', { conversations: list });

                                _.each(list, function (item) {
                                    joms.store.dispatch('chats/open', item.id);
                                    joms.store.dispatch('chats/addSidebarItem', item.id);
                                    joms.store.dispatch('chats/setTopSidebar', item.id);
                                });

                                _.each(json.buddies, function (buddy) {
                                    joms.store.commit('users/add', { data: buddy });
                                });
                            }
                        }, this)
                    });
                },

                /**
                 * Craft Show change group chat avatar.
                 * @param
                 * @returns
                 */
                changeGroupChatAva: function changeGroupChatAva(data) {

                    function saveAva(thumb, chatid) {
                        joms.ajax({
                            func: 'chat,ajaxSaveGroupChatAva',
                            data: [thumb,  chatid],
                            callback: $.proxy(function (r) {
                                if(r.ok) {
                                    let $avatar = $('.img-item img');
                                    $chat.html($avatar);
                                    $ul.remove();
                                } else if (!r.ok || r.error) {
                                    alert( r.error ? r.error : 'Ошибка при удалении пользователя')
                                }
                            }, this)
                        });
                    };

                    var $form = '<div class="form-row">\n' +
                        ' <div class="img-list" id="js-file-list"></div>\n' +
                        ' <input id="js-file" type="file" name="file[]" accept="image/*" hidden/>\n' +
                        '  <div class="form-submit">\n' +
                            '<label class="js-popup-button" for="js-file">Выбрать другое</label>\n' +
                        '  <button id="saveava" class="js-popup-button">Сохранть</button>\n' +
                        '  </div>',

                        $ul = $('<ul class="chat-menu" role="menu" >').append($form).appendTo('body').prop('hidden', true),
                        $chat = $('.joms-chat__item.active > div.avaava.joms-avatar > a');

                        $(document).click(  function (e) {
                            if(!e.target.matches('.chat-menu , .chat-menu label, .chat-menu input, .chat-menu button, a, .img-list, img-item') && $ul.is(':visible')) {
                                $ul.remove();
                            }
                        });

                    $('#js-file').click().change(function(){
                        if (window.FormData === undefined) {
                            alert('В вашем браузере загрузка файлов не поддерживается, а ведь уже 21-й первый век ;)');
                        } else {
                            var formData = new FormData();
                            $.each($("#js-file")[0].files, function(key, input){
                                formData.append('file[]', input);
                            });
                            $.ajax({
                                type: 'POST',
                                url: '/index.php?option=com_community&view=chat&task=ajaxUpload',
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: formData,
                                dataType : 'json',
                                success: function(msg){
                                    msg.forEach(function(row) {
                                        if (row.error == '') {
                                            $ul.show();
                                            $('#js-file-list').html(row.data);
                                           // $('#js-file-list').append(row.data); // for multiple
                                        } else {
                                            alert(row.error);
                                        }
                                    });
                                    $("#js-file").val('');
                                }
                            });

                        }
                    });

                    $('#saveava').click(function () {
                        saveAva($('.img-item > input').val() , data)
                    });

                },
                /**
                 * Craft Show group chat members list menu.
                 * @param
                 * @returns
                 */
                showGroupChatMembers: function showGroupChatMembers(data) {
                    // delete user from chat
                    function deleteUserFromChat(userid, chatid) {
                        joms.ajax({
                            func: 'chat,ajaxDeleteUserFromChat',
                            data: [userid,  chatid],
                            callback: $.proxy(function (r) {
                                if(r.ok) {
                                    $members.find('#li-'+ userid  +'').remove();
                                } else if (!r || r.error) {
                                    alert( r.error || 'Ошибка при удалении пользователя')
                                }
                            }, this)
                        });
                    };
                    // show user info menu in members list
                    function showUserMenu(userid, thumb, alias, name) {
                        // make popup user menu window
                        let usermenu = $('<ul id="ul-' + userid + '" class="chat-menu" role="menu" aria-labelledby="dLabel">' +
                            '<li>' +
                            '<img src="' + thumb + '" align="absmiddle"> ' +
                            ' ' + name +
                            '</li>' +
                            '<li>' +
                            '<a href="/component/community/' + alias + '/profile"> ' +
                            ' Профиль пользователя</a>' +
                            '</li>' +
                            '<li>' +
                            '<a href="javascript:joms.api.pmSend(' + userid + ')"> ' +
                            ' Отрпавить сообщение</a>' +
                            '</li>' +
                            '</ul>').appendTo('body');
                        // hide paret window
                        $members.fadeOut();

                        // if user is chat owner - show option 'delete member from chat'
                        if (data.owner) {
                            usermenu.append('<li class="remove" data-uid="' + userid + '">Удалить из чата</li>');
                            $('li.remove').click(function () {
                                if (confirm('Действительно хотите удалить этого пользователя из чата?')) {
                                    deleteUserFromChat($(this).data('uid'), data.chatid);
                                    $(this).parent().remove();
                                    $members.fadeIn();
                                }
                            });
                         }
                        // remove popup user menu window on click other area
                        $(document).click(  function (e) {
                            if(!e.target.matches('li, #ul-'+ userid)) {
                                usermenu.remove();
                                // show paret window again
                                $members.fadeIn();
                            }
                        });
                    }
                        /*** uncomment to make close button
                         var $close = $('<button>').addClass('removeAttachment').html('+').click(function(){
                            $members.remove();
                             $('body').css('overflow', 'auto');
                        }); ***/

                     // make popup members list body
                    var $members =  $('<ul>').addClass('chat-menu').appendTo('body'); //.prepend($close); uncomment to show close button

                        if($members) $('body').css('overflow', 'hidden');

                    // make popup members list body inner items
                    $.each(data.users, function(i,v) {
                        if(!v.thumb) {
                           v.thumb = '/components/com_community/assets/user-Male-thumb.png';
                        }
                        let $li = $('<li id="li-'+ v.userid +'"><img class="joms-avatar" src="'+ v.thumb +'">'+ v.name +'</li>').appendTo($members).click(function (){
                            showUserMenu(v.userid, v.thumb, v.alias, v.name);
                        });
                    });

                    // remove popup members list window on click other area
                    $(document).click(  function (e) {
                        if($members.is(':visible')) {
                            if (!e.target.matches('li, .chat-menu')) {
                                $members.remove();
                                $('body').css('overflow', 'auto');
                            }
                        }
                    });
                },
                /**
                 * Sends message.
                 * @param {string} message
                 * @returns jQuery.Deferred
                 */
                messageSend: function messageSend(message, attachment) {
                    this.ping.abort();
                    var partner = [],
                        name = '',
                        chat_id = 0;
                    if (+this.active.chat_id === 0) {
                        partner = this.active.partner;
                        name = this.active.name;
                        joms_observer.do_action('chat_selector_hide');
                        joms_observer.do_action('chat_selector_reset');
                        if (this.active.temp_chat_id) {
                            joms_observer.do_action('chat_hightlight_active_window', this.active.temp_chat_id);
                            joms_observer.do_action('chat_remove_draft_conversation');

                            this.removeDraftConversation();
                            chat_id = this.active.temp_chat_id;
                            this.setActiveChat(chat_id);
                        }
                    } else {
                        chat_id = +this.active.chat_id;
                    }
                    joms_observer.do_action('chat_move_window_to_top', [this.active]);
                    joms_observer.do_action('chat_move_notification_to_top', [this.active]);
                    joms_observer.do_action('chat_remove_seen_message');

                    return $.Deferred($.proxy(function (defer) {
                        var now = new Date().getTime();
                        joms_observer.do_action('chat_message_sending', message, attachment, this.me, now);

                        // Remove unneeded information.
                        attachment = $.extend({}, attachment || {});
                        delete attachment.name;
                        delete attachment.url;
                        var is_seen = 1; //Craft seens
                        var target = $('.joms-js--chat-item-'+chat_id).data('users-id'); //Craft users to contoller for push
                        console.log(target);
                        joms.ajax({
                            func: 'chat,ajaxAddChat',
                            data: [chat_id, message, JSON.stringify(attachment), JSON.stringify(partner), name, is_seen, target], //craft
                            callback: $.proxy(function (json) {
                                if (json.error) {
                                    joms_observer.do_action('chat_error_message', now);
                                    alert(json.error);
                                } else {
                                    joms_observer.do_action('chat_message_sent', json.reply_id, now, json.attachment || {}, is_seen); //craft

                                    // craft
                                    $('span.check-done-msg.unread.unrevive').html(' <span data-testid="msg-dblcheck" aria-label=" Доставлено " data-icon="" class="check-done-msg unread">\n' +
                                        '           <svg viewBox="0 0 16 15" width="16" height="15" class=""><path fill="currentColor" d="m15.01 3.316-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>\n' +
                                        '            </svg></span>');

                                    if (chat_id === 0) {
                                        this.conversations['chat_' + json.chat.chat_id] = this.formatData([json.chat], this.buddies)[0];
                                        this.active = this.conversations['chat_' + json.chat_id];
                                        this.setLocationHash(this.active.chat_id);
                                        joms_observer.do_action('chat_override_draft_chat_window', this.active);
                                        joms_observer.do_action('chat_hightlight_active_window', this.active.chat_id);
                                        joms_observer.do_action('chat_render_option_dropdown', this.active.type, this.active.mute);
                                        this.removeDraftConversation();
                                    }
                                }
                                defer.resolveWith(this, [json]);
                            }, this)
                        });
                    }, this));
                },

                /**
                 * Recall sent message.
                 * @param {number} msgId
                 * @returns jQuery.Deferred
                 */
                messageRecall: function messageRecall(msgId) {
                    return $.Deferred($.proxy(function (defer) {
                        joms.ajax({
                            func: 'chat,ajaxRecallMessage',
                            data: [msgId],
                            callback: $.proxy(function (json) {
                                defer.resolveWith(this, [json]);
                            }, this)
                        });
                    }, this));
                },

                /**
                 * Naively get friend list from `window.joms_friends` value.
                 * @returns jQuery.Deferred
                 */
                friendListUpdate: function friendListUpdate() {
                    return $.Deferred($.proxy(function (defer) {
                        var timer = setInterval($.proxy(function () {
                            if (!_.isUndefined(window.joms_friends)) {
                                clearInterval(timer);
                                var friends = [];
                                var joms_friends = window.joms_friends;
                                for (var i = 0; i < joms_friends.length; i++) {
                                    if (+joms_friends[i].id === +this.me.id) {
                                        this.buddySet({ id: this.me.id, name: 'You', avatar: joms_friends[i].avatar });
                                        this.me.name = '';//'Вы';
                                        this.me.avatar = joms_friends[i].avatar;
                                    } else {
                                        friends.push(joms_friends[i]);
                                    }
                                }
                                defer.resolveWith(this, [friends]);
                            }
                        }, this), 100);
                    }, this));
                },

                /**
                 * Add buddy list.
                 * @param {number} id
                 * @param {string} name
                 * @param {string} avatar
                 */
                buddyAdd: function buddyAdd(id, name, avatar) {
                    id = +id;
                    if (!this.buddies.hasOwnProperty(id)) {
                        this.buddies[id] = {
                            id: id,
                            name: name,
                            avatar: avatar
                        };
                    }
                },

                /**
                 * Get buddy information.
                 * @param {number} id
                 * @return {Object|undefined}
                 */
                buddyGet: function buddyGet(id) {
                    return this.buddies[id];
                },

                /**
                 * Set buddy information.
                 * @param {Object} data
                 * @param {number} data.id
                 * @param {string} data.name
                 * @param {string} data.avatar
                 * @param {boolean} data.online
                 */
                buddySet: function buddySet(data) {
                    var id = data.id;
                    var data = _.extend(this.buddies[id] || {}, data);

                    this.buddies[id] = data;
                },

                /**
                 * Update conversations based on current state.
                 * @return {_.debounce}
                 */
                updateConversations: function updateConversations() {
                    var active = this.active || {},
                        activeId = +active.chat_id;

                    _.each(this.conversations, function (item) {
                        var isActive = +item.chat_id === activeId,
                            copy = $.extend({}, item, { active: isActive });

                        joms_observer.do_action('chat_conversation_update', copy);
                    }, this);
                }

            };

            return Chat;
        });

    },{"./header":2,"./messagebox":3,"./messages":4,"./notification":5,"./sidebar":6}],2:[function(require,module,exports){
        'use strict';

        (function ($, _, Backbone) {

            var util = require('./util');

            /**
             * Conversation header view.
             * @class {Backbone.View}
             */
            module.exports = Backbone.View.extend({

                el: '.joms-js--chat-header',

                events: {
                    'click .joms-js--chat-new-message': 'createDraftConversation',
                    'click .joms-js--chat-header-selector-div [data-user-id]': 'selectorSelect',
                    'click .joms-js--chat-leave': 'leaveChat',
                    'click .joms-js--chat-mute': 'muteChat',
                    'click .joms-js--all-peoples': 'allPeoples',
                    'click .joms-js--change-background': 'changeBackground',
                    'click .joms-js--change-ava': 'changeAva',
                    'click .joms-js--chat-change-active-group-name': 'changeActiveGroupChatName',
                    'click .remove-selected-user': 'removeSelectedUser',
                    'click .joms-dropdown-button': 'bindActionToMobilePopup',
                    'keyup .joms-chat__search_user': 'actionOnSearchInput',
                    'keydown .joms-chat__search_user': 'removeLastSelectedUser'
                },

                initialize: function initialize() {
                    this.$info = this.$('.js-forhide');

                    this.$button = this.$info.find('.joms-js--chat-new-message');
                    this.$recipients = this.$info.find('.joms-chat__recipents');
                    this.$selector = this.$('.joms-js--chat-header-selector');
                    this.$selected = this.$selector.find('.joms-chat-selected');
                    this.$selectorInput = this.$selector.find('.joms-input');
                    this.$selectorDiv = this.$selector.find('.joms-js--chat-header-selector-div');
                    this.$optionDropdown = this.$('.joms-js--chat-dropdown');
                    this.$searchInput = this.$('.joms-chat__search_user');
                    this.searchword = '';

                    joms_observer.add_action('chat_selector_hide', this.selectorHide, 1, 0, this);
                    joms_observer.add_action('chat_selector_show', this.selectorShow, 1, 0, this);
                    joms_observer.add_action('chat_selector_reset', this.selectorReset, 1, 0, this);
                    joms_observer.add_action('chat_update_info', this.updateChatInfo, 1, 0, this);
                    joms_observer.add_action('chat_hide_new_message_button', this.hideNewMessageButton, 1, 0, this);
                    joms_observer.add_action('chat_show_new_message_button', this.showNewMessageButton, 1, 0, this);
                    joms_observer.add_action('chat_render_option_dropdown', this.renderOptionDropdown, 1, 3, this);
                },

                hideNewMessageButton: function hideNewMessageButton() {
                    this.$button.css('visibility', 'hidden');
                },

                showNewMessageButton: function showNewMessageButton() {
                    this.$button.css('visibility', '');
                },

                bindActionToMobilePopup: function bindActionToMobilePopup(e) {
                    var self = this;
                    if (joms.mobile) {
                        setTimeout(function () {
                            var $mobile_dropdown = $('.joms-popup .joms-js--chat-dropdown');
                            var $chat_leave = $mobile_dropdown.find('.joms-js--chat-leave');
                            //var $all_peoples = $mobile_dropdown.find('.joms-js--all-peoples');
                           // var $change_background = $mobile_dropdown.find('.joms-js--change-background');
                            //var $change_ava = $mobile_dropdown.find('.joms-js--change-ava');
                            var $chat_mute = $mobile_dropdown.find('.joms-js--chat-mute');
                            var $chang_name = $mobile_dropdown.find('.joms-js--chat-change-active-group-name');

                            $chat_leave.on('click', function () {
                                self.leaveChat();
                            });

                            // $all_peoples.on('click', function () {
                            //     self.allPeoples();
                            // });
                            //
                            // $change_background.on('click', function () {
                            //     self.changeBackground();
                            // });
                            //
                            // $change_ava.on('click', function () {
                            //     self.changeAva();
                            // });

                            $chat_mute.on('click', function (e) {
                                self.muteChat(e);
                            });

                            $change_name.on('click', function () {
                                self.changeActiveGroupChatName();
                            });
                        }, 300);
                    }
                },

                changeActiveGroupChatName: function changeActiveGroupChatName() {
                    var name = prompt(joms_lang.COM_COMMUNITY_CHAT_NAME_OF_CONVERSATION, '');
                    if (name == null) {
                        this.$optionDropdown.hide();
                        return;
                    }
                    var MAX_CHAR = 250,
                        MIN_CHAR = 4;
                    if (name && name.length < MIN_CHAR) {
                        alert(joms_lang.COM_COMMUNITY_CHAT_NAME_OF_CONVERSATION_SHOULD_NOT_BE_EMPTY);
                    } else if (name && name.length > MAX_CHAR) {
                        alert(joms_lang.COM_COMMUNITY_CHAT_NAME_OF_CONVERSATION_SHOULD_BE_LESS_THAN_250_CHARACTERS);
                    } else {
                        joms_observer.do_action('chat_change_active_group_chat_name', name);
                    }

                    this.$optionDropdown.hide();
                },

                actionOnSearchInput: function actionOnSearchInput(e) {
                    if (e.which === 27 /* Esc key */) {
                        this.selectorHide();
                        joms_observer.do_action('chat_selector_hide');
                        joms_observer.do_action('chat_selector_reset');
                        joms_observer.do_action('chat_remove_draft_conversation');
                        joms_observer.do_action('chat_open_first_window');
                        this.searchword = '';
                        return;
                    }

                    var self = this;
                    var keyword = self.$searchInput.val().trim();
                    var selected = self.$selected.find('.user-selected');
                    var exclusion = '';
                    if (selected.length) {
                        exclusion = _.map(selected, function (item) {
                            return $(item).data('user-id');
                        }).join(',');
                    }
                    self.$selectorDiv.empty();
                    self.$selectorDiv.hide();

                    if (keyword != self.searchword) {
                        clearTimeout(self.search);
                        self.searchword = keyword;
                        if (!keyword) {
                            return;
                        }
                        self.search = setTimeout(function () {
                            self.$selectorDiv.append('<img src="' + joms.BASE_URL + 'components/com_community/assets/ajax-loader.gif" alt="loader" />');
                            self.$selectorDiv.show();
                            joms.ajax({
                                func: 'chat,ajaxGetFriendListByName',
                                data: [keyword, exclusion],
                                callback: function callback(json) {
                                    if (Array.isArray(json) && json.length) {
                                        self.$selectorDiv.empty();
                                        self.selectorRender(json);
                                    } else {
                                        self.$selectorDiv.text(self.$selectorDiv.data('textNoResult'));
                                    }
                                }
                            });
                        }, 500);
                    }
                },

                leaveChat: function leaveChat() {
                    if (confirm(joms_lang.COM_COMMUNITY_CHAT_ARE_YOU_SURE_TO_LEAVE_THIS_CONVERSATION)) {
                        joms_observer.do_action('chat_leave');
                        this.$optionDropdown.hide();

                        if (joms.mobile) {
                            $('.mfp-bg').remove();
                            $('.mfp-wrap').remove();
                        }
                    }
                },

                allPeoples: function  allPeoples() {
                    joms_observer.do_action('all_peoples');
                    if (joms.mobile) {
                        // $('.mfp-bg').remove();
                        // $('.mfp-wrap').remove();
                    }
                },
                changeAva: function  changeAva(e) {
                    joms_observer.do_action('change_ava');
                    //this.$optionDropdown.hide();
                    if (joms.mobile) {
                    }
                },

                muteChat: function muteChat(e) {
                    var $dd = this.$optionDropdown,
                        $btn = $(e.currentTarget),
                        mute = +$btn.data('mute'),
                        text = $btn.data(mute ? 'textMute' : 'textUnmute');

                    $dd.hide();
                    $btn.html(text).data('mute', mute ? 0 : 1);
                    joms_observer.do_action('chat_mute', mute);
                },

                renderOptionDropdown: function renderOptionDropdown(type, mute, users) {
                    var $dd = this.$optionDropdown;
                    var $mute = $dd.find('.joms-js--chat-mute');
                    var $add = $dd.find('.joms-js--chat-add-people');
                    var $change_name = $dd.find('.joms-js--chat-change-active-group-name');

                    $mute.data('mute', +mute).html($mute.data(+mute ? 'textUnmute' : 'textMute'));
                    $add.attr('onclick', 'joms.popup.chat.addRecipient(\'' + users + '\')');
                    type === 'group' ? ($add.show(), $change_name.show()) : ($add.hide(), $change_name.hide());
                },

                removeLastSelectedUser: function removeLastSelectedUser(e) {
                    var keyword = this.$searchInput.val().trim();
                    if (e.which === 8 && !keyword) {
                        var selected = this.$selected.find('.user-selected').last();
                        selected.remove();
                        this.updateChatInfo();
                    }
                },

                removeSelectedUser: function removeSelectedUser(e) {
                    var $user = $(e.currentTarget).parent();
                    $user.remove();
                    this.updateChatInfo();
                },

                createDraftConversation: function createDraftConversation() {
                    this.selectorShow();
                    joms_observer.do_action('chat_create_draft_conversation');
                },

                /**
                 * Render friend selector.
                 * @param {object} buddies
                 */
                selectorRender: function selectorRender(buddies) {
                    _.each(buddies, function (buddy) {
                        this.$selectorDiv.append(['<div class="joms-chat__item joms-selector-', buddy.id, '" data-user-id="', buddy.id, '" style="padding:5px">', '<div class="joms-avatar ', buddy.online ? 'joms-online' : '', '">', '<a><img src="', buddy.avatar, '" /></a>', '</div>', '<div class="joms-chat__item-body">', '<a>', buddy.name, '</a>', '</div>', '</div>'].join(''));
                    }, this);
                },

                /**
                 * Show new chat selector.
                 * @params {HTMLEvent} e
                 */
                selectorShow: function selectorShow() {
                    this.$info.hide();
                    this.$selector.show();
                    this.$selectorInput.val('').focus();
                },

                /**
                 * Hide new chat selector.
                 */
                selectorHide: function selectorHide() {
                    this.$selectorInput.val('');
                    this.$selector.hide();
                    this.$info.show();
                    this.$selectorDiv.hide();
                },

                selectorReset: function selectorReset() {
                    this.$selected.empty();
                    this.$selectorDiv.empty();
                },

                /**
                 * Hide new chat selector if Esc key is pressed.
                 * @params {HTMLEvent} e
                 */
                selectorHideOnEscape: function selectorHideOnEscape(e) {},

                /**
                 * Hide new chat selector on input blur.
                 * @params {HTMLEvent} e
                 */
                selectorHideOnBlur: function selectorHideOnBlur(e) {
                    this.selectorHide();
                },

                /**
                 * Create new conversation with friend.
                 * @params {HTMLEvent} e
                 */
                selectorSelect: function selectorSelect(e) {
                    var $el = $(e.currentTarget),
                        user_id = $el.data('user-id'),
                        name = $el.find('.joms-chat__item-body').text(),
                        avatar = $el.find('img').attr('src'),
                        html;

                    html = ['<span class="user-selected" data-user-id="' + user_id + '">', name, '<i class="fa fa-times remove-selected-user"></i>', '</span>'].join('');

                    $el.hide();
                    this.$selected.append(html);
                    this.$searchInput.val('').focus();
                    this.$selectorDiv.hide();
                    this.updateChatInfo();
                    this.searchword = '';

                    joms_observer.do_action('chat_buddy_add', user_id, name, avatar);
                },

                updateChatInfo: function updateChatInfo() {
                    var user_selected,
                        partner = [],
                        name = [],
                        chatname = '';
                    user_selected = this.$selected.find('.user-selected');
                    if (user_selected.length < 1) {
                        joms_observer.do_action('chat_empty_message_view');
                        joms_observer.do_action('chat_disable_message_box');
                    } else if (user_selected.length === 1) {
                        name.push(user_selected.text());
                        joms_observer.do_action('chat_single_conversation_get_by_user', user_selected.data('user-id'));
                        joms_observer.do_action('chat_enable_message_box');
                    } else if (user_selected.length > 1) {
                        _.each(user_selected, function (item) {
                            partner.push($(item).data('user-id'));
                            name.push($(item).text());
                        }, this);
                        joms_observer.do_action('chat_empty_message_view');
                        joms_observer.do_action('chat_enable_message_box');
                    }

                    if (name.length) {
                        chatname = util.formatName(name);
                    }

                    joms_observer.do_action('chat_update_draft_conversation', chatname, partner);
                    joms_observer.do_action('rename_chat_title', chatname);
                }

            });
        })(joms.jQuery, joms._, joms.Backbone);

    },{"./util":7}],3:[function(require,module,exports){
        'use strict';

        (function ($, _, Backbone) {

            /**
             * Conversation sidebar view.
             * @class {Backbone.View}
             */
            module.exports = Backbone.View.extend({

                el: '.joms-chat__messagebox',

                events: {
                    'click .joms-js--send': 'messageSend',
                    'keydown textarea': 'messageSendOnEnter'
                },

                initialize: function initialize() {
                    this.$wrapper = this.$('.joms-js-wrapper');
                    this.$disabler = this.$('.joms-js-disabler');
                    this.$textarea = this.$('textarea');
                    // this.$textarea = this.$('.craft-chat__message'); // craft make div like textarea
                    this.$thumbnail = this.$('.joms-textarea__attachment--thumbnail');

                    joms_observer.add_action('chat_conversation_open', this.render, 10, 2, this);
                    joms_observer.add_action('chat_conversation_update', this.update, 10, 1, this);
                    joms_observer.add_action('chat_disable_message_box', this.disableMessageBox, 10, 1, this);
                    joms_observer.add_action('chat_enable_message_box', this.enableMessageBox, 10, 1, this);
                },

                render: function render() {
                    this.$textarea.val('');

                    // this.$textarea.text('');  // craft make div like textarea
                },

                update: function update(item) {
                    if (!item.active) {
                        return;
                    }

                    if (item.type === 'group' && !+item.participants) {
                        this.$disabler.show();
                        this.$textarea.attr('disabled', 'disabled');
                    } else {
                        this.$disabler.hide();
                        this.$textarea.removeAttr('disabled');
                    }
                },

                disableMessageBox: function disableMessageBox() {
                    this.$disabler.show();
                    this.$textarea.attr('disabled', 'disabled');
                },

                enableMessageBox: function enableMessageBox() {
                    this.$disabler.hide();
                    this.$textarea.removeAttr('disabled');
                },

                messageSend: function messageSend(e) {
                    // var msg = $.trim(this.$textarea.text()), // craft make div like textarea
                    var msg = $.trim(this.$textarea.val()),
                        $draft = $('.joms-js--chat-item-0 '),
                        $attachment = jQuery('.joms-textarea__attachment--thumbnail'),
                        $file = $attachment.children('b'),
                        $img = $attachment.children('img'),
                        attachment;

                    // Exit on new message if no user is selected.
                    if ($draft.length && !$('.user-selected').length) {
                        return;
                    }

                    // Handle file attachment parameter.
                    if ($file.length) {
                        attachment = {
                            type: 'file',
                            id: $file.data('id'),
                            url: $file.data('path'),
                            name: $file.data('name')
                        };
                        this.$wrapper.find('.removeAttachment').click();
                        $file.remove();

                        // Handle image attachment parameter.
                    } else if ($img.attr('src') && $img.attr('src').match(/\.(gif|jpe?g|png)$/i)) {
                        attachment = {
                            type: 'image',
                            id: $img.data('photo_id'),
                            url: $img.attr('src')
                        };
                        this.$wrapper.find('.removeAttachment').click();

                        // Handle empty attachment.
                    } else {
                        attachment = '';
                    }

                    if (msg || attachment) {
                        joms_observer.do_action('chat_messagebox_send', msg, attachment);
                        this.$textarea.val('');
                        e.preventDefault();
                    }
                },

                messageSendOnEnter: function messageSendOnEnter(e) {
                    if (e.which === 13 && e.shiftKey) {
                        this.messageSend(e);
                    }
                }
            });
        })(joms.jQuery, joms._, joms.Backbone);

    },{}],4:[function(require,module,exports){
        'use strict';

        (function ($, _, Backbone) {

            var util = require('./util');

            /**
             * Conversation messages view.
             * @class {Backbone.View}
             */
            module.exports = Backbone.View.extend({

                el: '.joms-chat__messages',

                events: {
                    'click .joms-chat__message-actions a': 'recallMessage',
                    'mouseenter [data-tooltip]': 'showTooltip',
                    'mouseleave [data-tooltip]': 'hideTooltip'
                },

                initialize: function initialize(config) {
                    this.$loading = this.$('.joms-js--chat-conversation-loading');
                    this.$messages = this.$('.joms-js--chat-conversation-messages');
                    this.$noParticipants = this.$('.joms-js--chat-conversation-no-participants');

                    joms_observer.add_action('chat_conversation_open', this.render, 10, 2, this);
                    joms_observer.add_action('chat_conversation_update', this.update, 10, 1, this);
                    joms_observer.add_action('chat_messages_loading', this.messagesLoading, 10, 1, this);
                    joms_observer.add_action('chat_messages_loaded', this.messagesLoaded, 10, 3, this);
                    joms_observer.add_action('chat_messages_received', this.messagesReceived, 10, 3, this);
                    joms_observer.add_action('chat_messages_render', this.messagesRender, 10, 10, this);
                    joms_observer.add_action('chat_message_sending', this.messageSending, 10, 5, this);
                    joms_observer.add_action('chat_message_sent', this.messageSent, 10, 3, this);
                    joms_observer.add_action('chat_empty_message_view', this.emptyView, 1, 0, this);
                    joms_observer.add_action('chat_seen_message', this.seenMessage, 1, 3, this);
                    joms_observer.add_action('chat_remove_seen_message', this.removeSeenMessage, 1, 2, this);
                    joms_observer.add_action('chat_previous_messages_loaded', this.previousMessagesLoaded, 1, 2, this);
                    joms_observer.add_action('chat_error_message', this.errorMessage, 1, 1, this);

                    // Handle scrolling through the messages.
                    this.$messages.on('mousewheel DOMMouseScroll', $.proxy(this.onScroll, this));
                },

                render: function render() {
                    this.$messages.empty().hide();
                    this._updateRecallAbility();
                },

                update: function update(item) {
                    var participants;

                    if (!item.active) {
                        return;
                    }

                    participants = +item.participants;
                    if (item.type !== 'group') {
                        participants = 1;
                    }

                    this._toggleEmptyParticipants(participants);
                },

                errorMessage: function errorMessage(now) {
                    var $error = this.$messages.find('[data-timestamp=' + now + ']');
                    $error.addClass('joms-chat__message-error');
                    $error.find('.joms-js-chat-loading').hide();
                    $error.find('.joms-chat__message-actions').hide();
                },

                /**
                 * Get older messages for current conversation.
                 */
                getOlderMessages: _.debounce(function () {
                    var $ct = this.$messages,
                        $end = $ct.children('.joms-js--chat-conversation-end'),
                        $msg,
                        msgId;

                    // Do not proceed if all older messages are already loaded.
                    if ($end.length) {
                        return;
                    }

                    // Get ID of the oldest message.
                    $msg = $ct.find('.joms-js-chat-content[data-id]').first();
                    msgId = $msg.data('id');

                    // Get previous messages if ID found.
                    if (msgId) {
                        this.$loading.show();
                        joms_observer.do_action('chat_get_previous_messages', null, msgId);
                    }
                }, 500, true),

                seenMessage: function seenMessage(data, me, buddies) {
                    var seen, names, template, html, $seen;

                    this.$seenses = this.$('.chat-time.you.sending-now');

                    if (!(_.isArray(data) && data.length)) {
                        return;
                    }

                    seen = _.chain(data).filter(function (item) {
                        return +me.id !== +item.user_id;
                    }).map(function (item) {
                        return buddies[item.user_id];
                    }).value();

                    if (!seen.length) {
                        return;
                    }


                    // Merge with previous seen users.
                    this._seen = _.chain((this._seen || []).concat(seen)).uniq(function (item) {
                        return +item.id;
                    }).sortBy(function (item) {
                        return item.name;
                    }).value();

                    // Removes previous seen html.
                    // $seen = this.$messages.children('.joms-js--seen');
                    $seen = this.$seenses.children('.joms-js--seen');
                    if ($seen.length) {
                        $seen.remove();
                    }

                    // Render new seen html.
                    template = util.getTemplateById('joms-js-template-chat-seen-by');
                    names = _.map(this._seen, function (item) {
                        return item.name;
                    });

                    // www = _.filter(data, $.proxy(function (item) {
                    //     return item.is_seen;
                    // }, this));

                    html = template({
                        seen: seen,
                        item: data[0],
                        names: util.formatName(names)
                    });
                    $seen = $(html).addClass('joms-js--seen');
                    this.$('span.check-done-msg.unread.unrevive').remove();


                    this.$seenses.append($seen);
                    //this.$messages.append($seen);
                    this.scrollToBottom();
                },

                removeSeenMessage: function removeSeenMessage() {
                    this._seen = false;
                    //this.$messages.children('.joms-js--seen').remove();
                    //this.$seenses.children('.joms-js--seen').remove();
                },

                emptyView: function emptyView() {
                    this._seen = false;
                    this.$loading.hide();
                    this.$messages.empty().show().css('opacity', '');
                },

                messagesLoading: function messagesLoading() {
                    this.$messages.css('opacity', 0);
                    this.$loading.show();
                },

                messagesLoaded: function messagesLoaded(data, buddies) {
                    this.$loading.hide();
                    this.$messages.css('opacity', '');
                    // var timeoffset = (new Date).getTimezoneOffset() * 60; // craft user location time offset

                    data.reverse();
                    _.each(data, function (item) {
                        var user = buddies[item.user_id];
                        var time = item.created_at * 1000;
                        // var time = (item.created_at - timeoffset) * 1000; // craft fix display craft user time // 09.07.22 now it fix in model "chat"  addchat() - save to db local time
                        this.messagesRender(item.id, item.content, item.attachment ? JSON.parse(item.attachment) : {}, user, time, item.action, item.params ? JSON.parse(item.params) : {}, item.is_seen);
                    }, this);
                    this._updateRecallAbility();
                    this.scrollToBottom();
                },

                messagesRender: function messagesRender(id, message, attachment, user, timestamp, action, params, is_seen) {
                    var $container = this.$messages,
                        date = new Date(timestamp),
                        timeFormatted = util.formatTime(timestamp),//util.formatDateTime(timestamp), // Craft to seen only time in message
                        dGroup,
                        $dGroup,
                        template,
                        html,
                        $last,
                        name,
                        mine;

                    // Get date group for messages.
                    dGroup = date.toJSON().slice(0, 10).replace(/-/g, '');
                    $dGroup = $container.children('.joms-js-chat-message-dgroup').filter('[data-id="' + dGroup + '"]');

                    if (!$dGroup.length) {
                        template = util.getTemplateById('joms-tpl-chat-message-dgroup');
                        $dGroup = $(template({ id: dGroup, date: util.formatDate(timestamp) }));
                        $dGroup.appendTo($container);
                    }

                    $container = $dGroup.children('.joms-js-content');

                    mine = user && +user.id === +window.joms_my_id || false;
                    name = mine ? 'you' : '';

                    if (action === 'sent') {

                        // Format links.
                        message = message.replace(/((http|https):\/\/.*?[^\s]+)/g, '<a target="_blank" style="text-decoration:underline" href="$1">$1</a>');

                        // Replace newlines.
                        message = message.replace(/\\n/g, '<br />');
                        message = message.replace(/\r?\n/g, '<br />');

                        var att = '';
                        if (attachment.type) {
                            att = this.attachmentView(attachment);
                        }

                        $last = $container.find('.joms-chat__message-item').last();

                        if (!$last.length || +$last.data('user-id') !== +user.id) {
                            if (user.name.indexOf('<') >= 0) {
                                var span = document.createElement('span');
                                span.innerHTML = user.name;
                                user.name = span.innerText;
                            }

                            template = util.getTemplateById('joms-js-template-chat-message');
                            html = template({
                                timestamp: timestamp,
                                name: name,
                                user_id: user.id,
                                user_name: user.name,
                                user_avatar: user.avatar,
                                online: user.online,
                                profile_link: user.profile_link
                            });

                            $last = $(html);
                            $last.appendTo($container);
                        }

                        template = util.getTemplateById('joms-js-template-chat-message-content');
                        html = template({
                            message: util.getEmoticon(message),
                            name: name, // craft
                            time: timeFormatted,
                            timestamp: timestamp,
                            id: id,
                            attachment: att,
                            seen: this._seen, // craft
                            mine: mine,
                            is_seen: is_seen // craft
                        });
                        $last.find('.joms-js-chat-message-item-body').append(html);
                    } else if (action === 'leave') {
                        template = util.getTemplateById('joms-js-template-chat-leave');
                        html = template({
                            id: id,
                            mine: mine,
                            name: user.name,
                            time: timeFormatted
                        });
                        $container.append(html);
                    } else if (action === 'add') {
                        template = util.getTemplateById('joms-js-template-chat-added');
                        html = template({
                            id: id,
                            mine: mine,
                            name: user.name,
                            time: timeFormatted
                        });
                        $container.append(html);
                    } else if (action === 'change_chat_name') {
                        template = util.getTemplateById('joms-js-template-chat-name-changed');
                        html = template({
                            id: id,
                            mine: mine,
                            name: user.name,
                            groupname: _.escape(params.groupname),
                            time: timeFormatted
                        });
                        $container.append(html);
                        this.scrollToBottom();
                    }
                },

                previousMessagesLoaded: function previousMessagesLoaded(data, buddies) {
                    this.$loading.hide();
                    if (!data.length) {
                        return;
                    }

                    _.each(data, function (item) {
                        var user = buddies[item.user_id];
                        var time = item.created_at * 1000;
                        this.preMessagesRender(item.id, item.content, JSON.parse(item.attachment), user, time, item.action, item.is_seen);
                    }, this);

                    this._updateRecallAbility();

                    var parent_offset = this.$messages.offset();
                    var first_element = data[0];
                    var first_item = this.$messages.find('.joms-chat__message-content[data-id="' + first_element.id + '"]');
                    var offset = first_item.offset();
                    var padding_top = +this.$messages.css('padding-top').replace('px', '');
                    this.$messages.scrollTop(offset.top - parent_offset.top - padding_top);
                },

                preMessagesRender: function preMessagesRender(id, message, attachment, user, timestamp, action, is_seen) {
                    var $container = this.$messages,
                        date,
                        timeFormatted,
                        dGroup,
                        $dGroup,
                        template,
                        html,
                        $first,
                        name,
                        mine;

                    // Special case on end message.
                    if (action === 'end') {
                        template = util.getTemplateById('joms-js-template-chat-message-end');
                        html = template();
                        $container.prepend(html);
                        return;
                    }

                    // Format date and time.
                    date = new Date(timestamp), timeFormatted = util.formatDateTime(timestamp),

                        // Get date group for messages.
                        dGroup = date.toJSON().slice(0, 10).replace(/-/g, '');
                    $dGroup = $container.children('.joms-js-chat-message-dgroup').filter('[data-id="' + dGroup + '"]');

                    if (!$dGroup.length) {
                        template = util.getTemplateById('joms-tpl-chat-message-dgroup');
                        $dGroup = $(template({ id: dGroup, date: util.formatDate(timestamp) }));
                        $dGroup.prependTo($container);
                    }

                    $container = $dGroup.children('.joms-js-content');

                    mine = user && +user.id === +window.joms_my_id || false;
                    name = mine ? 'you' : '';

                    if (action === 'sent') {

                        // Format links.
                        message = message.replace(/((http|https):\/\/.*?[^\s]+)/g, '<a target="_blank" style="text-decoration:underline" href="$1">$1</a>');

                        // Replace newlines.
                        message = message.replace(/\\n/g, '<br />');
                        message = message.replace(/\r?\n/g, '<br />');

                        var att = '';
                        if (attachment.type) {
                            att = this.attachmentView(attachment);
                        }

                        $first = $container.find('.joms-chat__message-item').first();

                        if (!$first.length || +$first.data('user-id') !== +user.id) {
                            if (user.name.indexOf('<') >= 0) {
                                var span = document.createElement('span');
                                span.innerHTML = user.name;
                                user.name = span.innerText;
                            }

                            template = util.getTemplateById('joms-js-template-chat-message');
                            html = template({
                                timestamp: timestamp,
                                name: name,
                                user_id: user.id,
                                user_name: user.name,
                                user_avatar: user.avatar,
                                online: user.online
                            });

                            $first = $(html);
                            $first.prependTo($container);
                        }

                        template = util.getTemplateById('joms-js-template-chat-message-content');
                        html = template({
                            message: util.getEmoticon(message),
                            name: name, // craft
                            time: timeFormatted,
                            timestamp: timestamp,
                            id: id,
                            date: date,
                            attachment: att,
                            mine: mine
                        });
                        $first.find('.joms-js-chat-message-item-body').prepend(html);
                    } else if (action === 'leave') {
                        template = util.getTemplateById('joms-js-template-chat-leave');
                        html = template({
                            id: id,
                            mine: mine,
                            name: user.name,
                            time: timeFormatted
                        });
                        $container.prepend(html);
                    } else if (action === 'add') {
                        template = util.getTemplateById('joms-js-template-chat-added');
                        html = template({
                            id: id,
                            mine: mine,
                            name: user.name,
                            time: timeFormatted
                        });
                        $container.prepend(html);
                    }
                },

                messagesReceived: function messagesReceived(data, buddies) {
                    if (data.length > 0) {
                        _.each(data, function (item) {
                            var user = buddies[item.user_id];
                            var time = item.created_at * 1000;
                            this.messagesRender(item.id, item.content, item.attachment ? JSON.parse(item.attachment) : {}, user, time, item.action, item.params ? JSON.parse(item.params) : {});
                        }, this);
                        this.scrollToBottom();
                    }
                },

                attachmentView: function attachmentView(attachment) {
                    var type = attachment.type,
                        template;

                    if (!attachment.url) {
                        return '';
                    } else if (type === 'file') {
                        template = util.getTemplateById('joms-js-template-chat-message-file');
                        return template({ url: attachment.url, name: attachment.name });
                    } else if (type === 'image') {
                        template = util.getTemplateById('joms-js-template-chat-message-image');
                        return template({ url: attachment.url });
                    } else if (type === 'video') {
                        template = util.getTemplateById('joms-js-template-chat-message-video');
                        return template($.extend({ url: attachment.url }, attachment.video));
                    } else if (type === 'url') {
                        template = util.getTemplateById('joms-js-template-chat-message-url');
                        return template({
                            url: attachment.url,
                            title: attachment.title,
                            images: attachment.images,
                            description: attachment.description
                        });
                    }
                },

                messageAppend: function messageAppend(message, attachment, me, timestamp) {
                    this.messagesRender(null, message, attachment, me, timestamp, 'sent');
                },

                messageSending: function messageSending(message, attachment, me, timestamp) {
                    message = _.escape(message);
                    this.messageAppend(message, attachment, me, timestamp);
                    this.scrollToBottom();

                    // Show loading if ajax request is taking too long.
                    setTimeout($.proxy(function () {
                        var $msg = this.$messages.find('.joms-js-chat-content.' + timestamp),
                            $loading = $msg.siblings('.joms-js-chat-loading'),
                            is_error = !!$msg.parents('.joms-chat__message-error').length;

                        if (!is_error && $loading.length) {
                            $loading.show();
                        }
                    }, this), 1500);
                },

                messageSent: function messageSent(id, timestamp, attachment) {
                    var $msg = this.$messages.find('.joms-js-chat-content.' + timestamp),
                        $loading = $msg.siblings('.joms-js-chat-loading'),
                        $attachment;

                    $msg.siblings('.chat-time.you').addClass('sending-now'); // Craft

                    $msg.attr('data-id', id);

                    $loading.remove();



                    // Updates link preview.
                    if (attachment && (attachment.type === 'url' || attachment.type === 'video')) {
                        $attachment = $msg.next('.joms-js-chat-attachment');
                        if ($attachment) {
                            $attachment.remove();
                        }
                        $attachment = $(this.attachmentView(attachment));
                        $attachment.insertAfter($msg);
                    }
                },

                recallMessage: function recallMessage(e) {
                    if (!confirm(joms_lang.COM_COMMUNITY_CHAT_ARE_YOU_SURE_TO_DELETE_THIS_MESSAGE)) {
                        return;
                    }

                    var $btn = $(e.currentTarget).closest('.joms-chat__message-actions'),
                        $msg = $btn.siblings('.joms-chat__message-content'),
                        $group = $msg.closest('.joms-chat__message-item'),
                        isMine = +$group.data('user-id') === +window.joms_my_id,
                        id = +$msg.data('id'),
                        $prevGroup,
                        $nextGroup;

                    e.preventDefault();
                    e.stopPropagation();

                    if (isMine) {
                        $msg = $msg.parent();

                        if ($msg.siblings().length) {
                            $msg.remove();
                        } else {
                            $prevGroup = $group.prev();
                            $nextGroup = $group.next();
                            $group.remove();

                            if (+$prevGroup.data('user-id') === +$nextGroup.data('user-id')) {
                                $prevGroup.find('.joms-chat__message-body').children().prependTo($nextGroup.find('.joms-chat__message-body'));
                                $prevGroup.remove();
                            }
                        }

                        joms_observer.do_action('chat_message_recall', id);
                    }
                },

                scrollToBottom: function scrollToBottom() {
                    var div = this.$messages[0];
                    div.scrollTop = div.scrollHeight;
                },

                _updateRecallAbility: function _updateRecallAbility() {
                    var now = new Date().getTime(),
                        maxElapsed = +joms.getData('chat_recall'),
                        $btns;

                    if (!maxElapsed) {
                        return;
                    }

                    $btns = this.$messages.find('.joms-chat__message-actions');
                    if ($btns.length) {
                        maxElapsed = maxElapsed * 60 * 1000;
                        $btns.each(function () {
                            var $btn = $(this),
                                ts = +$btn.parent().data('timestamp');

                            if (ts && now - ts > maxElapsed) {
                                $btn.remove();
                            }
                        });
                    }

                    // Check every 30s.
                    clearInterval(this._checkRecallTimer);
                    this._checkRecallTimer = setInterval($.proxy(this._updateRecallAbility, this), 30 * 1000);
                },

                _toggleEmptyParticipants: function _toggleEmptyParticipants(count) {
                    if (count > 0) {
                        this.$noParticipants.hide();
                    } else {
                        this.$noParticipants.show();
                    }
                },

                showTooltip: function showTooltip(e) {
                    var that = this;

                    this._tooltipTimer = setTimeout(function () {
                        var $el = $(e.currentTarget),
                            tooltip = $el.data('tooltip'),
                            position = $el.offset();

                        if (!that.$tooltip) {
                            that.$tooltip = $('<div class="joms-tooltip joms-js-chat-tooltip" />').appendTo(document.body);
                        }

                        that.$tooltip.html(tooltip).css(position).show();

                        // Adjust position.
                        that.$tooltip.css({
                            left: position.left - that.$tooltip.outerWidth() - 10,
                            top: position.top + $el.outerHeight(), // craft  / 2,
                            transform: 'translateY(-50%)'
                        });
                    }, 800);
                },

                hideTooltip: function hideTooltip() {
                    clearTimeout(this._tooltipTimer);

                    if (this.$tooltip) {
                        this.$tooltip.hide();
                    }
                },

                onScroll: function onScroll(e) {
                    var $ct, height, delta, scrollTop, scrollHeight;

                    e.stopPropagation();

                    $ct = this.$messages;
                    delta = e.originalEvent.wheelDelta || -e.originalEvent.detail;
                    scrollTop = $ct.scrollTop();

                    // Reaching the top-most of the div.
                    if (delta > 0 && scrollTop <= 0) {

                        // Try to load older messages.
                        try {
                            this.getOlderMessages();
                        } catch (e) {}

                        return false;
                    }

                    height = $ct.outerHeight();
                    scrollHeight = $ct[0].scrollHeight;

                    // Reaching the bottom-most of the div.
                    if (delta < 0 && scrollTop >= scrollHeight - height) {
                        return false;
                    }

                    return true;
                }

            });
        })(joms.jQuery, joms._, joms.Backbone);

    },{"./util":7}],5:[function(require,module,exports){
        'use strict';

        (function ($, _, Backbone, observer) {

            var util = require('./util');

            /**
             * Conversation notification view.
             * @class
             */
            function Notification() {
                $($.proxy(this.initialize, this));
            }

            Notification.prototype = {

                initialize: function initialize() {
                    this.$el = $('.joms-js--notification-chat-list').add('.joms-js--notification-chat-list-mobile');
                    this.$popover = $('.joms-popover--toolbar-chat');
                    this.$counter = $('.joms-js--notiflabel-chat');

                    observer.add_action('chat_conversation_render', this.render, 10, 1, this);
                    observer.add_action('chat_noconversation_render', this.render, 10, 1, this);
                    observer.add_action('chat_set_notification_label', this.updateCounter, 10, 1, this);
                    observer.add_action('chat_set_notification_label_seen', this.markItemAsRead, 10, 1, this);
                    observer.add_action('chat_set_notification_label_unread', this.markItemAsUnread, 10, 1, this);
                    observer.add_action('chat_move_notification_to_top', this.moveItemToTop, 10, 1, this);
                    observer.add_action('chat_removemove_notification', this.removeItem, 10, 1, this);
                    observer.add_action('chat_all_marked_read', this.markAllItemAsRead, 1, 1, this);

                    $(document).on('click', '.joms-js-chat-notif', $.proxy(this.onItemClick, this));

                    this.$popover.on('wheel', function (e) {
                        var height = $(this).height();
                        var scrollHeight = this.scrollHeight;
                        var scrollTop = this.scrollTop;
                        var delta = e.originalEvent.deltaY;
                        var dir = delta > 0 ? 'down' : 'up';
                        if (scrollTop === scrollHeight - height && dir === 'down') {
                            e.preventDefault();
                        }

                        if (scrollTop === 0 && dir === 'up') {
                            e.preventDefault();
                        }
                    });
                },

                render: function render(data) {
                    $('.joms-popover--toolbar-chat .joms-js--loading').hide();

                    $('.joms-js--notification-toolbar').show();

                    if (!Object.keys(data).length) {
                        $('.joms-popover--toolbar-chat .joms-js--empty').show();
                        return;
                    }

                    var baseURI = joms.getData('chat_base_uri'),
                        html = '',
                        template;

                    if (!(template = this._renderTemplate)) {
                        template = joms.getData('chat_template_notification_item');
                        template = this._renderTemplate = util.template(template);
                    }

                    data = $.extend({}, data || {});

                    _.each(data, function (item) {
                        item.name = util.formatName(item.name);

                        // Normalize avatar url.
                        if (item.thumb && !item.thumb.match(/^https?:\/\//i)) {
                            item.thumb = baseURI + item.thumb;
                        }

                        $('.joms-popover--toolbar-chat').each(function (i, e) {
                            $(e).children('.joms-js--empty').hide();

                            // prevent duplicated render
                            if ($(e).find('.joms-js-chat-notif-' + item.chat_id).length) {
                                return;
                            }

                            var btnfull = $(e).find('.joms-js--notification-toolbar');
                            $(template(item)).insertBefore(btnfull);
                        });
                    }, this);

                    $(document).on('click', '.joms-js-mask_all_as_read', $.proxy(this.markAllAsRead, this));
                },

                updateCounter: function updateCounter(newValue) {
                    var oldValue = +this.$counter.text();
                    if (+newValue !== oldValue) {
                        this.$counter.text(+newValue || '');
                    }
                },

                markAllAsRead: function markAllAsRead() {
                    var $popover = $('.joms-popover--toolbar-chat');
                    $popover.hide();
                    $popover.closest('.joms-popup__wrapper').click();
                    observer.do_action('chat_mark_all_as_read');
                },

                markAllItemAsRead: function markAllItemAsRead() {
                    this.$el.find('.unread').each(function () {
                        $(this).removeClass('unread');
                    });
                    this.updateCounter(0);
                },

                markItemAsRead: function markItemAsRead(id) {
                    this.$popover.find('.joms-js-chat-notif-' + id).removeClass('unread');
                },

                markItemAsUnread: function markItemAsUnread(id) {
                    this.$popover.find('.joms-js-chat-notif-' + id).addClass('unread');
                },

                moveItemToTop: function moveItemToTop(list) {
                    _.each(list, function (item) {
                        this.$popover.each(function () {
                            $(this).prepend($(this).find('.joms-js-chat-notif-' + item.chat_id));
                        });
                    }, this);
                },

                removeItem: function removeItem(id) {
                    this.$popover.find('.joms-js-chat-notif-' + id).remove();
                },

                onItemClick: function onItemClick(e) {
                    var $item = $(e.currentTarget),
                        id = $item.data('chat-id'),
                        isChatView = joms.getData('is_chat_view'),
                        chatURI = joms.getData('chat_uri'),
                        $popover;

                    e.preventDefault();
                    e.stopPropagation();

                    if (isChatView) {
                        $popover = $('.joms-popover--toolbar-chat');
                        $popover.hide();
                        $popover.closest('.joms-popup__wrapper').click();
                        observer.do_action('chat_open_window_by_chat_id', id);
                        observer.do_action('chat_sidebar_select', id);
                        observer.do_action('chat_set_location_hash', id);
                        return;
                    }

                    window.location = chatURI + '#' + id;
                }

            };

            module.exports = Notification;
        })(joms.jQuery, joms._, joms.Backbone, joms_observer);

    },{"./util":7}],6:[function(require,module,exports){
        'use strict';

        (function ($, _, Backbone) {

            var util = require('./util');

            /**
             * Conversation sidebar view.
             * @class {Backbone.View}
             */
            module.exports = Backbone.View.extend({

                el: '.joms-chat__conversations-wrapper',

                events: {
                    'click .joms-chat__item': 'itemSelect',
                    'wheel .joms-js-list': 'scrollSidebar',
                    'keyup .joms-chat__search_conversation': 'searchConversation',
                    'focus .joms-chat__search_conversation': 'showSearchResultsBox',
                    'click .search-close': 'onSearchClose'
                },

                initialize: function initialize() {
                    this.$list = this.$('.joms-js-list');
                    this.$loading = this.$list.find('.joms-js--chat-sidebar-loading');
                    this.$notice = this.$('.joms-js-notice');
                    this.$searchInput = this.$('.joms-chat__search_conversation');
                    this.$searchBox = this.$('.joms-chat__search-box');
                    this.$closeBtn = this.$searchBox.find('.search-close');

                    this.$searchResults = this.$('.joms-chat__search-results');

                    this.$groupResults = this.$searchResults.find('.joms-js__group-results');
                    this.$groupLoading = this.$groupResults.next('.joms-js--chat-sidebar-loading');

                    this.$contactResults = this.$searchResults.find('.joms-js__contact-results');
                    this.$contactLoading = this.$contactResults.next('.joms-js--chat-sidebar-loading');

                    this.searching = 0;
                    this.no_conversation_left = false;
                    this.limit = +joms.getData('message_sidebar_softlimit');

                    joms_observer.add_action('chat_user_login', this.userLogin, 10, 1, this);
                    joms_observer.add_action('chat_user_logout', this.userLogout, 10, 1, this);
                    joms_observer.add_action('chat_conversation_render', this.renderListConversation, 1, 1, this);
                    joms_observer.add_action('chat_conversation_open', this.conversationOpen, 10, 1, this);
                    joms_observer.add_action('chat_update_preview_message', this.updatePreviewMessage, 10, 5, this);
                    joms_observer.add_action('chat_highlight_unread_windows', this.hightlighUnreadWindows, 1, 1, this);
                    joms_observer.add_action('chat_hightlight_active_window', this.highlightActiveWindow, 1, 1, this);
                    joms_observer.add_action('rename_chat_title', this.renameChatTitle, 1, 1, this);
                    joms_observer.add_action('chat_override_draft_chat_window', this.overrideDraftChatWindow, 1, 1, this);
                    joms_observer.add_action('chat_remove_draft_conversation', this.removeDraftConversation, 1, 0, this);
                    joms_observer.add_action('chat_open_first_window', this.openFirstWindow, 1, 0, this);
                    joms_observer.add_action('chat_render_draft_conversation', this.renderDraftConversation, 1, 1, this);
                    joms_observer.add_action('chat_open_window_by_chat_id', this.openWindowByChatId, 1, 1, this);
                    joms_observer.add_action('chat_set_window_seen', this.setWindowSeen, 1, 1, this);
                    joms_observer.add_action('chat_move_window_to_top', this.moveWindowToTop, 1, 1, this);
                    joms_observer.add_action('chat_remove_window', this.removeWindow, 1, 1, this);
                    joms_observer.add_action('chat_mute', this.muteChat, 1, 1, this);
                    joms_observer.add_action('chat_all_marked_read', this.setAllWindowSeen, 1, 1, this);
                    joms_observer.add_action('sidebar_change_conversation_name', this.changeConversationName, 1, 2, this);
                },

                changeConversationName: function changeConversationName(name, chat_id) {
                    var $conv = this.$list.find('.joms-js--chat-item-' + chat_id);
                    $conv.find('.joms-chat__item-body b').html(name);
                },

                /**
                 * Update sidebar on login event.
                 */
                userLogin: function userLogin() {
                    this.$loading.hide();
                    this.$notice.hide();
                    this.$list.show();
                },

                /**
                 * Update sidebar on logout event.
                 */
                userLogout: function userLogout() {
                    this.$loading.hide();
                    this.$list.hide();
                    this.$notice.show();
                },

                showSearchResultsBox: function showSearchResultsBox() {
                    this.$list.hide();
                    this.$searchResults.show();
                    this.$closeBtn.show();
                    joms_observer.do_action('chat_hide_new_message_button');
                },

                onSearchClose: function onSearchClose() {
                    this.hideSearchResultsBox(true);
                },

                hideSearchResultsBox: function hideSearchResultsBox(open) {
                    this.resetSearchResults();
                    this.$searchInput.val('').trigger('keyup'); // hide result results
                    this.$searchResults.hide();
                    this.$closeBtn.hide();
                    this.$list.show();
                    var $active = this.$list.find('.joms-chat__item.active');
                    if (!$active.length && open) {
                        this.openFirstWindow();
                    }

                    joms_observer.do_action('chat_show_new_message_button');
                },

                searchConversation: function searchConversation(e) {
                    var keyword = this.$searchInput.val().toLowerCase();

                    if (keyword === this.keyword) {
                        return;
                    }

                    this.keyword = keyword;

                    this.resetSearchResults();
                    if (keyword.length < 2) {
                        this.$groupLoading.hide();
                        this.$contactLoading.hide();
                        return;
                    }

                    if (e.which < 112 && e.which > 47 || e.which === 8 || e.which === 16) {
                        clearTimeout(this.searching);

                        this.$groupLoading.show();
                        this.$contactLoading.show();

                        var items = this.$list.find('.joms-chat__item'),
                            self = this,
                            exclusion = [],
                            state,
                            fetchtime,
                            no_contact_template,
                            no_group_template;

                        _.each(items, function (item) {
                            var name = $(item).find('b').text().toLowerCase(),
                                id = $(item).data('chat-id'),
                                type = $(item).data('chat-type');

                            exclusion.push(id);

                            if (name.indexOf(keyword) === -1) {
                                return;
                            }

                            var $clone = $(item).clone();
                            $clone.removeClass('active').addClass('result-item');

                            if (type === 'group') {
                                self.$groupResults.append($clone);
                            }

                            if (type === 'single') {
                                self.$contactResults.append($clone);
                            }
                        });

                        self._fetchTime = fetchtime = new Date().getTime();
                        self.searching = setTimeout(function () {
                            joms.ajax({
                                func: 'chat,ajaxSearchChat',
                                data: [keyword, exclusion.join(',')],
                                callback: function callback(json) {
                                    if (self._fetchTime !== fetchtime) {
                                        return;
                                    }

                                    if (json.error) {
                                        alert(json.error);
                                        return;
                                    }

                                    var data = {};
                                    _.each(json.single, function (item) {
                                        var html = self.renderSearchResult(item);
                                        self.$contactResults.append(html);
                                        data['chat_' + item.chat_id] = item;
                                    });

                                    if (!json.single.length && !self.$contactResults.html()) {
                                        no_contact_template = util.getTemplateById('joms-js-template-chat-no-contact-found');
                                        self.$contactResults.html(no_contact_template());
                                    }
                                    self.$contactLoading.hide();

                                    _.each(json.group, function (item) {
                                        var html = self.renderSearchResult(item);
                                        self.$groupResults.append(html);

                                        item.name = util.formatName(item.name);
                                        data['chat_' + item.chat_id] = item;
                                    });

                                    joms_observer.do_action('chat_add_conversions', data);

                                    if (!json.group.length && !self.$groupResults.html()) {
                                        no_contact_template = util.getTemplateById('joms-js-template-chat-no-group-found');
                                        self.$groupResults.html(no_contact_template());
                                    }

                                    self.$groupLoading.hide();
                                }
                            });
                        }, 500);
                    }
                },

                renderSearchResult: function renderSearchResult(data) {
                    var template = util.getTemplateById('joms-js-template-chat-sidebar-search-result-item'),
                        html;

                    html = template({
                        id: data.chat_id,
                        type: data.type,
                        name: util.formatName(data.name).replace(/<img(.*?)\/>/, ''),
                        avatar: data.thumb
                    });

                    return html;
                },

                resetSearchResults: function resetSearchResults() {
                    this.$contactResults.html('');
                    this.$groupResults.html('');
                },

                scrollSidebar: function scrollSidebar(e) {
                    var height = this.$list.height();
                    var scrollHeight = this.$list[0].scrollHeight;
                    var scrollTop = this.$list[0].scrollTop;
                    var delta = e.originalEvent.deltaY;
                    var dir = delta > 0 ? 'down' : 'up';
                    if (scrollTop === scrollHeight - height && dir === 'down') {
                        e.preventDefault();
                        if (!this.no_conversation_left) {
                            if (this.$loading.is(':hidden')) {
                                this.$list.append(this.$loading);
                                this.$loading.show();

                                var ids = [];
                                var items = this.$list.find('.joms-chat__item');
                                for (var i = 0; i < items.length; i++) {
                                    var item = items[i];
                                    ids.push(this.$(item).attr('data-chat-id'));
                                }
                                this.loadMoreConversation(JSON.stringify(ids));
                            }
                        }
                    }

                    if (scrollTop === 0 && dir === 'up') {
                        e.preventDefault();
                    }
                },

                loadMoreConversation: function loadMoreConversation(ids) {
                    var self = this;
                    joms.ajax({
                        func: 'chat,ajaxInitializeChatData',
                        data: [ids],
                        callback: function callback(data) {

                            var numList = Object.keys(data.list).length;
                            if (numList) {
                                joms_observer.do_action('chat_conversation_render', data.list);
                                joms_observer.do_action('chat_add_conversions', data.list);
                            }

                            if (numList < self.limit) {
                                self.no_conversation_left = true;
                            }

                            var numBudy = Object.keys(data.buddies).length;
                            if (numBudy) {
                                for (var key in data.buddies) {
                                    var budy = data.buddies[key];
                                    joms_observer.do_action('chat_buddy_add', budy.id, budy.name, budy.avatar);
                                }
                            }
                            self.$loading.hide();
                        }
                    });
                },

                muteChat: function muteChat(mute) {
                    var mute_icon = ['<div class="joms-chat__item-actions">', '<svg viewBox="0 0 16 16" class="joms-icon">', '<use xlink:href="#joms-icon-close"></use>', '</svg>', '</div>'].join('');
                    var active = this.$list.find('.active');
                    if (mute) {
                        active.find('.joms-chat__item-actions').remove();
                    } else {
                        active.append(mute_icon);
                    }
                },

                removeWindow: function removeWindow(chat_id) {
                    this.$list.find('.joms-js--chat-item-' + chat_id).remove();
                },

                moveWindowToTop: function moveWindowToTop(list) {
                    for (var i = 0; i < list.length; i++) {
                        var $item = this.$list.find('.joms-js--chat-item-' + list[i].chat_id);
                        if ($item.length) {
                            this.$list.prepend($item);
                        } else {
                            // render searched item after sending message
                            var template = util.getTemplateById('joms-js-template-chat-sidebar-item'),
                                html,
                                data;

                            data = list[i];

                            html = template({
                                id: data.chat_id,
                                type: data.type,
                                name: util.formatName(data.name).replace(/<img(.*?)\/>/, ''),
                                unread: false,
                                active: true,
                                online: data.online,
                                avatar: data.thumb
                            });

                            this.$list.prepend(html);
                        }
                    }

                    this.hideSearchResultsBox();
                },

                setAllWindowSeen: function setAllWindowSeen() {
                    this.$list.find('.joms-chat__item.unread').each(function () {
                        $(this).removeClass('unread');
                    });
                },

                setWindowSeen: function setWindowSeen(chat_id) {
                    this.$list.find('.joms-js--chat-item-' + chat_id).removeClass('unread');
                },

                renderDraftConversation: function renderDraftConversation(data) {
                    var template = util.getTemplateById('joms-js-template-chat-sidebar-draft'),
                        html = template();

                    this.$list.prepend(html);
                    this.$list.find('.joms-js--remove-draft').on('click', function () {
                        joms_observer.do_action('chat_selector_hide');
                        joms_observer.do_action('chat_selector_hide');
                        joms_observer.do_action('chat_selector_reset');
                        joms_observer.do_action('chat_remove_draft_conversation');
                        joms_observer.do_action('chat_open_first_window');
                    });
                },

                openFirstWindow: function openFirstWindow() {
                    var item = this.$list.find('.joms-chat__item').first(),
                        chat_id = item.data('chat-id');
                    if (chat_id) {
                        this.itemSetActive(item);
                        joms_observer.do_action('chat_sidebar_select', item.data('chat-id'));
                    }
                },

                openWindowByChatId: function openWindowByChatId(chat_id) {
                    var item = this.$list.find('.joms-js--chat-item-' + chat_id);
                    this.itemSetActive(item);
                    joms_observer.do_action('chat_sidebar_select', chat_id);
                },

                removeDraftConversation: function removeDraftConversation() {
                    this.$list.find('.joms-js--chat-item-0').remove();
                },

                overrideDraftChatWindow: function overrideDraftChatWindow(data) {
                    var item = $(this.$list.find('.active')),
                        avatar = item.find('.joms-avatar img');
                    item.attr('data-chat-type', data.type);
                    item.attr('data-chat-id', data.chat_id);
                    item.removeClass('joms-js--chat-item-0').addClass('joms-js--chat-item-' + data.chat_id);
                    avatar.attr('src', data.thumb);
                },

                renameChatTitle: function renameChatTitle(name) {
                    var item = this.$list.find('.active').find('.joms-chat__item-body b');
                    item.text(name);
                },

                /**
                 * Render all conversation items.
                 * @param {object[]} data
                 */
                renderListConversation: function renderListConversation(data) {
                    var $startScreen = $('.joms-js-page-chat-loading'),
                        $chatScreen = $('.joms-js-page-chat'),
                        key;

                    if ($chatScreen.is(':hidden')) {
                        $chatScreen.show();
                        $startScreen.hide();
                    }

                    for (key in data) {
                        this.render(data[key]);
                    }
                },

                /**
                 * Render a conversation item.
                 * @param {object} data
                 */
                render: function render(data) {
                    var template = util.getTemplateById('joms-js-template-chat-sidebar-item'),
                        isActive = false,
                        isUnread = !+data.seen,
                        html,
                        $item;

                    // Check if item is already exist.
                    $item = this.$list.children('.joms-js--chat-item-' + data.chat_id);
                    if ($item.length && $item.hasClass('active')) {
                        isActive = true;
                        isUnread = false;
                    }
                    //console.log(data);
                    html = template({
                        id: data.chat_id,
                        type: data.type,
                        users_id: data.users,
                        name: util.formatName(data.name).replace(/<img(.*?)\/>/, ''),
                        unread: isUnread,
                        active: isActive,
                        online: data.online,
                        avatar: data.thumb,
                        mute: +data.mute
                    });

                    if ($item.length) {
                        $item.replaceWith(html);
                    } else {
                        this.$list.append(html);
                    }
                    // craft make msg counter on avatars in chat
                    joms.ajax({
                        func: 'chat,ajaxGetUnreadMsg',
                        data: [data.chat_id],
                        // callback: function (json) {
                        //     // @todo maybe anything need for callback
                        // }
                    }).success(function (r) {
                        let count = $('.joms-chat__item.joms-js--chat-item-'+ data.chat_id +' span.msgcount');
                        count.attr('data-unread', r.length);
                        count.data('unread') == 0 ? '' : count.text(r.length);
                    });

                },
                prependRender: function prependRender(data) {
                    var template = joms.getData('chat_page_list') || '',
                        html;

                    html = template.replace(/##type##/g, data.type).replace(/##chat_id##/g, data.chat_id).replace(/##name##/g, data.name).replace(/##thumb##/g, data.thumb).replace(/##unread##/g, '').replace(/##mute##/g, '');

                    this.$list.prepend(html);
                },

                /**
                 * Show particular conversation item.
                 * @param {HTMLEvent} e
                 */
                itemSelect: function itemSelect(e) {
                    e.preventDefault();
                    var $elm = $(e.currentTarget),
                        chatId = $elm.data('chat-id'),
                        $item = this.$list.find('.joms-js--chat-item-' + chatId);

                    if ($item.length) {
                        this.itemSetActive($item);
                    } else {
                        this.setInactiveAll();
                    }

                    if (this.$searchInput.val()) {
                        this.$searchInput.val('');
                        this.$list.find('.joms-chat__item').show();
                    }

                    joms_observer.do_action('chat_sidebar_select', chatId);
                    if (chatId > 0) {
                        joms_observer.do_action('chat_selector_hide');
                    } else {
                        joms_observer.do_action('chat_selector_show');
                    }
                },

                /**
                 * Set active item on conversation open.
                 * @param {jQuery} $item
                 */
                itemSetActive: function itemSetActive($item) {
                    $item.siblings('.active').removeClass('active');
                    $item.removeClass('unread').addClass('active');
                    $item.find('.msgcount').text('');
                },

                setInactiveAll: function setInactiveAll() {
                    this.$list.find('.joms-chat__item').removeClass('active');
                },

                /**
                 * Handle open conversation.
                 * @param {number} userId
                 */
                conversationOpen: function conversationOpen(chatId) {
                    var $item = this.$list.find('.joms-js--chat-item-' + chatId);
                    if ($item.length) {
                        this.itemSetActive($item);
                    }
                },

                /**
                 * Change display message below avatar.
                 * @param {object} message
                 * @param {object} active
                 */
                updatePreviewMessage: function updatePreviewMessage(message, active) {
                    var $item;
                    if (active && active.user_id) {
                        $item = this.$list.find('.joms-js--chat-item-user-' + active.user_id);
                        if ($item.length) {
                            $item.find('.joms-js--chat-item-msg').text(message);
                        }
                    }
                },

                /**
                 * Highlight active sidebar item.
                 * @param {Number} chat_id
                 */
                highlightActiveWindow: function highlightActiveWindow(chat_id) {
                    var $item = this.$list.find('.joms-js--chat-item-' + chat_id);
                    this.itemSetActive($item);
                },

                /**
                 * Highlight unread sidebar items.
                 * @param {Object[]} data
                 */
                hightlighUnreadWindows: function hightlighUnreadWindows(data) {
                    _.each(data, function (item) {
                        var $item = this.$('.joms-js--chat-item-' + item.chat_id);
                        if (!$item.hasClass('active')) {
                            $item.addClass('unread');

                            joms.ajax({
                                func: 'chat,ajaxGetUnreadMsg',
                                data: [item.chat_id],
                                // callback: function (json) {
                                //     // @todo maybe anything need for callback
                                // }
                            }).success(function (r) {
                                let count = $item.find('span.msgcount');
                                count.attr('data-unread', r.length);
                                r.length == 0 ? '' : count.text(r.length);
                            });



                        }
                    }, this);
                }

            });
        })(joms.jQuery, joms._, joms.Backbone);

    },{"./util":7}],7:[function(require,module,exports){
        (function (global){
            'use strict';

            (function (_) {

                var lang = window.joms_lang && joms_lang.date || {},
                    moment = (typeof window !== "undefined" ? window['joms'] : typeof global !== "undefined" ? global['joms'] : null).moment,
                    templatesCache = {};

                moment.updateLocale('jomsocial', {
                    parentLocale: 'en',
                    months: lang.months,
                    monthsShort: _.map(lang.months, function (s) {
                        return s.substr(0, 3);
                    }),
                    weekdays: lang.days,
                    weekdaysShort: _.map(lang.days, function (s) {
                        return s.substr(0, 3);
                    }),
                    weekdaysMin: _.map(lang.days, function (s) {
                        return s.substr(0, 2);
                    })
                });

                module.exports = {

                    /**
                     * Underscore template wrapper.
                     * @param {String} templateString
                     * @return {Function}
                     */
                    template: function template(templateString, settings) {
                        return _.template(templateString, {
                            variable: 'data',
                            evaluate: /\{\{([\s\S]+?)\}\}/g,
                            interpolate: /\{\{=([\s\S]+?)\}\}/g,
                            escape: /\{\{-([\s\S]+?)\}\}/g
                        });
                    },

                    /**
                     * Get template already defined in the HTML document.
                     * @param {String} id
                     * @return {Function}
                     */
                    getTemplateById: function getTemplateById(id) {
                        var template = templatesCache[id];

                        if (!template) {
                            template = document.getElementById(id).innerText;
                            // HACK: Joomla (or is it the browser?) is automatically added relative path after an `src="` string. Duh!
                            template = template.replace(/(src|href)="[^"]+\{\{/g, '$1="{{');
                            template = templatesCache[id] = this.template(template);
                        }

                        return template;
                    },

                    /**
                     * Format timestamp to a human-readable date string.
                     * @param {Number} timestamp
                     * @return {String}
                     */
                    formatDate: function formatDate(timestamp) {
                        var now = moment(),
                            date = moment(timestamp),
                            format = 'D MMM';

                        if (now.year() !== date.year()) {
                            format = 'D/MMM/YY';
                        }

                        return date.format(format);
                    },

                    /**
                     * Format timestamp to a human-readable time string.
                     * @param {Number} timestamp
                     * @return {String}
                     */
                    formatTime: function formatTime(timestamp) {
                        var time = moment(timestamp),
                            format = joms.getData('chat_time_format') || 'g:i A';

                        // PHP-to-Moment time format conversion.
                        format = format.replace(/[GH]/g, 'H').replace(/[gh]/g, 'h').replace(/i/ig, 'mm').replace(/s/ig, 'ss');

                        return time.format(format);
                    },

                    /**
                     * Format timestamp to a human-readable datetime string.
                     * @param {Number} timestamp
                     * @return {String}
                     */
                    formatDateTime: function formatDateTime(timestamp) {
                        var dateStr = this.formatDate(timestamp),
                            timeStr = this.formatTime(timestamp);

                        return dateStr + ' ' + timeStr;
                    },

                    /**
                     * Format name to proper punctuation.
                     * @param {String|String[]} names
                     * @return {String}
                     */
                    formatName: function formatName(names) {
                        var textAnd = joms.getData('chat_text_and');

                        if (!_.isArray(names)) {
                            names = [names];
                        }

                        if (names.length === 1) {
                            return names[0];
                        }

                        if (names.length > 1) {
                            names = _.map(names, function (str, span) {
                                // Remove badge on group conversations.
                                if (str.indexOf('<') >= 0) {
                                    span = document.createElement('span');
                                    span.innerHTML = str;
                                    str = span.innerText;
                                }

                                str = str.split(' ');
                                return str[0];
                            });
                            names = names.sort();
                            names = names.join(', ');
                            names = names.replace(/,\s([^\s]*)$/, ' ' + textAnd + ' $1');
                            return names;
                        }

                        return '';
                    },

                    /**
                     * Convert emoticon code into actual emoticon.
                     * @param {String} str
                     * @return {String}
                     */
                    getEmoticon: function getEmoticon(str) {
                        var emoticons = joms.getData('joms_emo'),
                            codes = [],
                            names = [];

                        _.each(emoticons, function (emo, name) {
                            codes.unshift(emo);
                            names.unshift(name);
                        });

                        _.each(codes, function (code, idx) {
                            _.each(code, function (c) {
                                str = str.replace(c, '<span class="joms-content-emo2 joms-emo2 joms-emo2-' + names[idx] + '"></span>');
                            });
                        });

                        return str;
                    }

                };
            })(joms._);

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{}]},{},[1]);
