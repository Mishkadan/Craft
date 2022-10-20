<?php
/**
 * @copyright (C) 2016 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityChatController extends CommunityBaseController
{

    //this should be the main page for chat
    public function display($cacheable = false, $urlparams = false)
    {
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $view = $this->getView('chat', '', $viewType);
        echo $view->get('display');
    }

    public function ajaxGetSingleChatByUser($user_id)
    {
        $model = CFactory::getModel('chat');
        die(json_encode($model->getSingleChatByUser($user_id)));
    }

    /**
     * @param $to
     * @param $message
     * @param $latestMessageId
     * @return either the chat id if successfully send through, else get a false
     */
    public function ajaxAddChat($chatid, $message, $attachment, $partner = '[]', $name = '', $is_seen = 1, $target)
    {

        $message = trim($message);

        $attachment = json_decode( $attachment );
        if (!$attachment) {
            $attachment = json_decode('{}');
        }

        if ($message || !empty($attachment->id)) {
            // Parse link.
            $urlPattern = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
            if (preg_match($urlPattern, $message)) {
                $graphObject = CParsers::linkFetch($message);
                if ($graphObject) {
                    if (!isset($attachment->type)) {
                        $attachment->type = 'url';
                    }

                    $attachment->url = $graphObject->get('url');
                    $attachment->title = $graphObject->get('title');
                    $attachment->description = $graphObject->get('description');
                    $attachment->images = $graphObject->get('image');

                    // Check if it is a video url (YouTube, Vimeo, etc).
                    $video = JTable::getInstance('Video', 'CTable');
                    if ($video->init($attachment->url)) {
                        $attachment->type = 'video';
                        $attachment->video = array(
                            'type' => $video->type,
                            'id' => $video->video_id,
                            'path' => $video->path,
                            'thumbnail' => $video->getThumbnail(),
                            'title' => $video->title,
                            'title_short' => JHTML::_('string.truncate', $video->title, 50, true, false),
                            'desc_short' => JHTML::_('string.truncate', $video->description, CFactory::getConfig()->getInt('streamcontentlength'), true, false)
                        );
                    }
                }
            }

            #TODO: check has access to chat
            $model = CFactory::getModel('chat');
            if ($chatid) {
                // Craft add push <--
                $date	= JDate::getInstance();
                $my = CFactory::getUser();
                $notification		= new stdClass;;
                $notification->actor	= $my->id;
                $notification->target	= array_diff(explode(',', $target), array($my->id));
                $notification->url	= JUri::root().'chat#'.$chatid;
                $notification->content	= $message;
                $notification->created	=  $date->toSql();
                $notification->params	= '{}';
                $notification->cmd_type	= 'push';
                $notification->type		= 0;

                if ($attachment && $attachment->type == 'image') {
                    $photoTable = JTable::getInstance('Photo', 'CTable');
                    $photoTable->load($attachment->id);
                    $notification->thumb = $photoTable->getThumbURI();
                    $notification->content	= 'Делится с вами фото.';
                }

                include_once 'plugins/community/jpjomsocial/jpjomsocial.php';

                foreach ($notification->target as $onetarget) {
                    $notification->target	= $onetarget;
                    plgCommunityJpjomsocial::onNotificationAdd($notification);
                }
                //-->

                die(json_encode($model->addChat($chatid, $message, $attachment)));
            } else {
                $my = CFactory::getUser();
                $partners = json_decode($partner);

                $result = $model->createChat($message, $attachment, $partner, $name, $is_seen);

                // Add user points.
                CUserPoints::assignPoint('inbox.message.send');

                // Add notification.
                $chat_id = $result->chat_id;

                $params = new CParameter('');
                $params->set('url', 'index.php?option=com_community&view=chat#' . $chat_id);

                $body = htmlspecialchars($message);
                $pattern = "/<br \/>/i";
                $replacement = "\r\n";
                $body = preg_replace($pattern, $replacement, $body);

                $params->set('message', $body);
                $params->set('title', JText::_('COM_COMMUNITY_PRIVATE_MESSAGE'));
                $params->set('msg_url', 'index.php?option=com_community&view=chat#' . $chat_id);
                $params->set('msg', JText::_('COM_COMMUNITY_PRIVATE_MESSAGE'));

                foreach ($partners as $to) {
                    CNotificationLibrary::add('inbox_create_message', $my->id, $to, JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE'), '', 'inbox.sent', $params);
                }

                die(json_encode($result));
            }
        } else {
            die('{}');
        }
    }

    /**
     * Ping the server to find out if there is any new message for the current user.
     * If there is a new message, it will return the message information, same structure as getLastChat
     * OR return false if there is nothing new
     */
    public function ajaxPingChat($last_activity = 0)
    {
        $model = CFactory::getModel('chat');
        die(json_encode($model->getActivity($last_activity)));
    }

    /**
     * Retrive the last x amount of message if specified, else we will retrieve from admin settings
     * @param $chatId
     * @param int $total
     * @param int $lastID
     */
    public function ajaxGetLastChat($chat_id, $offset = 0, $seen = 1, $is_seen = 1)
    {
        $model = CFactory::getModel('chat');
        $config = CFactory::getConfig();

        if ( $offset > 0 ) {
            $limit = $config->get('message_total_loaded_display', 10);
        } else {
            $limit = $config->get('message_total_initial_display', 10);
        }

        $data = $model->getLastChat($chat_id, $offset, $limit, $seen, $is_seen);
        die( json_encode($data) );
    }

    public function ajaxGetChatList($ids)
    {
        $ids = json_decode($ids);
        $model = CFactory::getModel('chat');
        die(json_encode($model->getChatList($ids)));
    }

    /**
     * Pass in the message id and that's it
     * @param $chatReplyId
     * @return true or false.
     */
    public function ajaxRecallMessage($chatReplyId){
        $model = CFactory::getModel('chat');
        die(json_encode($model->recallMessage($chatReplyId)));
    }

    /**
     * Gets all the chat windows from current user, with one message each
     * Returns all the chat windows with one latest chat info.
     * avatar = receiver avatar, chat_id = chat id
     */
    public function ajaxInitializeChatData($existed = '', $opened = '')
    {
        $existed = json_decode($existed);
        $existed = is_array($existed) ? $existed : array();

        $opened = json_decode($opened);
        $opened = is_array($opened) ? $opened : array();

        $model = CFactory::getModel('chat');
        $results = $model->initializeChatData($existed, $opened);
        die(json_encode($results));
    }

    public function ajaxSeen($chat_id)
    {

        $model = CFactory::getModel('chat');
        die($model->seen( (int) $chat_id ));
    }

    public function getUnreadMsg($chatid) //  Craft
    {
        $my = CFactory::getUser();

        $query = 'SELECT id FROM `#__community_chat_activity`'
            .' WHERE chat_id = ' . $chatid
            .' AND user_id != '.$my->id
            //.' AND action = "sent"'
            .' AND is_seen != 2'; //  Craft
        $db = JFactory::getDbo();

        return $db->setQuery($query)->loadColumn();
    }

    public function ajaxSetMessagesSeen($chatid) //  Craft
    {
        $my = CFactory::getUser();
        $model = CFactory::getModel('chat');
        $db  = JFactory::getDbo();
        $ids = $this->getUnreadMsg($chatid);

        if (!$ids) {
            die('Нет непрочитанных чатов');
        }

        foreach ($ids as $id)
        {
            $query = 'UPDATE `#__community_chat_activity` SET is_seen = 2 WHERE id = ' . $id;
            $db->setQuery($query)->execute();
        }

        die('ok');

        //die('Проверка остатка ' . var_dump($this->ajaxGetUnreadMsg($chatid)));
    }



    public function ajaxPrivateMessageSend($to, $msg, $attachment)
    {
        $attachment = json_decode( $attachment );
        if (!$attachment) {
            $attachment = json_decode('{}');
        }

        $my = CFactory::getUser();
        $model = CFactory::getModel('chat');
        $result = $model->addPrivateMessage($to, $msg, $attachment);
        if (isset($result->error)) {
            die(json_encode($result->error));
        }

        // Add user points.
        CUserPoints::assignPoint('inbox.message.send');

        // Add notification.
        $chat_id = $result->chat_id;

        $params = new CParameter('');
        $params->set('url', 'index.php?option=com_community&view=chat#' . $chat_id);

        $body = htmlspecialchars($msg);
        $pattern = "/<br \/>/i";
        $replacement = "\r\n";
        $body = preg_replace($pattern, $replacement, $body);

        $params->set('message', $body);
        $params->set('title', JText::_('COM_COMMUNITY_PRIVATE_MESSAGE'));
        $params->set('msg_url', 'index.php?option=com_community&view=chat#' . $chat_id);
        $params->set('msg', JText::_('COM_COMMUNITY_PRIVATE_MESSAGE'));

        CNotificationLibrary::add('inbox_create_message', $my->id, $to, JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE'), '', 'inbox.sent', $params);

        die(json_encode(JText::_('COM_COMMUNITY_INBOX_MESSAGE_SENT')));
    }

    public function ajaxLeaveChat($chat_id)
    {
        $model = CFactory::getModel('chat');
        $model->leaveChat($chat_id);
        die();
    }

    public function ajaxAddPeople($chat_id, $user_ids)
    {
        $user_ids = json_decode($user_ids);
        $model = CFactory::getModel('chat');
        $result = $model->addPeople($chat_id, $user_ids);
        die(json_encode($result));
    }

    public function ajaxGetFriendListByName($keyword, $exclusion)
    {
        $model = CFactory::getModel('chat');
        // $ids = $model->getFriendListByName($keyword, $exclusion);
        $ids = $model->getCraftusersId($keyword); // cradt search all users
        $result = array();
        if (count($ids)) {
            foreach ($ids as $id) {
                $profile = CFactory::getUser($id);
                $user = new stdClass;
                $user->name = $profile->getDisplayName();
                $user->id = $profile->id;
                $user->avatar = $profile->getThumbAvatar();
                $user->online = $profile->isOnline();
                $result[] = $user;
            }
        }
        die(json_encode($result));
    }

    public function ajaxMuteChat($chat_id, $mute)
    {
        $model = CFactory::getModel('chat');
        $model->muteChat($chat_id, $mute);
        die();
    }

    public function ajaxDisableChat($chat_id)
    {
        $model = CFactory::getModel('chat');
        $model->disableChat($chat_id);
        die();
    }

    public function ajaxMarkAllAsRead() {
        $model = CFactory::getModel('chat');
        $model->markAllAsRead();
        die();
    }

    public function ajaxChangeGroupChatName($chat_id, $name) {
        $model = CFactory::getModel('chat');
        $result = $model->changeGroupChatName($chat_id, $name);
        die(json_encode($result));
    }

    public function ajaxSearchChat($keyword = '', $exclusion = '') {
        $model = CFactory::getModel('chat');
        $result = $model->searchChat($keyword, $exclusion);
        die(json_encode($result));
    }
    public function ajaxGetUnreadMsg($chatid) //  Craft
    {
        $my = CFactory::getUser();

        $db = JFactory::getDbo();
        $query = 'SELECT id , chat_id, user_id, to_user FROM `#__community_chat_activity`'
            .' WHERE to_user LIKE '.$db->quote('%'.$my->id.'%')
            .' AND chat_id = ' . $chatid
            .' AND user_id != '.$my->id
            .' AND action = "sent"'
            .' AND is_seen != 2';

        die(json_encode($db->setQuery($query)->loadObjectList()));
    }

    public function ajaxAllPeoples($chatid)
    {
        $model = CFactory::getModel('chat');
        $result = $model->showMembersGroupChat($chatid);
        die(json_encode($result));
    }
    public function ajaxDeleteUserFromChat($userid, $chatid) {

        $model = CFactory::getModel('chat');
        die($model->deleteUserFromChat($userid, $chatid));
    }

    public function ajaxChangeGroupChatAva ($chatid) {
        die($chatid);
    }

    public function ajaxUpload() {

        header('Content-Type: application/json');

        $mainframe	= JFactory::getApplication();
        $jinput 	= $mainframe->input;
        $chat_backgroung = $jinput->get('action') == 'chat_backgroung';

        $input_name = 'file';

        if (!isset($_FILES[$input_name])) {
            exit;
        }

        $allow = array('jpg', 'jpeg', 'png', 'gif');
        $url_path = '/tmp/';
        $tmp_path =  JPATH_ROOT . $url_path;

        if (!is_dir($tmp_path)) {
            mkdir($tmp_path, 0777, true);
        }

        $files = array();
        $diff = count($_FILES[$input_name]) - count($_FILES[$input_name], COUNT_RECURSIVE);
        if ($diff == 0) {
            $files = array($_FILES[$input_name]);
        } else {
            foreach($_FILES[$input_name] as $k => $l) {
                foreach($l as $i => $v) {
                    $files[$i][$k] = $v;
                }
            }
        }

        $response = array();
        foreach ($files as $file) {
            $error = $data  = '';

            $ext = mb_strtolower(mb_substr(mb_strrchr(@$file['name'], '.'), 1));
            if (!empty($file['error']) || empty($file['tmp_name']) || $file['tmp_name'] == 'none') {
                $error = 'Не удалось загрузить файл.';
            } elseif (empty($file['name']) || !is_uploaded_file($file['tmp_name'])) {
                $error = 'Не удалось загрузить файл.';
            } elseif (empty($ext) || !in_array($ext, $allow)) {
                $error = 'Недопустимый тип файла';
            } else {
                $info = @getimagesize($file['tmp_name']);
                if (empty($info[0]) || empty($info[1]) || !in_array($info[2], array(1, 2, 3))) {
                    $error = 'Недопустимый тип файла';
                } else {
                    $name  = time() . '-' . mt_rand(1, 9999999999);
                    $src   = $tmp_path . $name . '.' . $ext;
                    $thumb = $tmp_path . $name . '-thumb.' . $ext;

                    if (move_uploaded_file($file['tmp_name'], $src)) {

                        switch ($info[2]) {
                            case 1:
                                $im = imageCreateFromGif($src);
                                imageSaveAlpha($im, true);
                                break;
                            case 2:
                                $im = imageCreateFromJpeg($src);
                                break;
                            case 3:
                                $im = imageCreateFromPng($src);
                                imageSaveAlpha($im, true);
                                break;
                        }

                        $width  = $info[0];
                        $height = $info[1];

                        // Высота превью 100px, ширина рассчитывается автоматически.
                        $h = 100;
                        $w = ($h > $height) ? $width : ceil($h / ($height / $width));
                        $tw = ceil($h / ($height / $width));
                        $th = ceil($w / ($width / $height));

                        $new_im = imageCreateTrueColor($w, $h);
                        if ($info[2] == 1 || $info[2] == 3) {
                            imagealphablending($new_im, true);
                            imageSaveAlpha($new_im, true);
                            $transparent = imagecolorallocatealpha($new_im, 0, 0, 0, 127);
                            imagefill($new_im, 0, 0, $transparent);
                            imagecolortransparent($new_im, $transparent);
                        }

                        if ($w >= $width && $h >= $height) {
                            $xy = array(ceil(($w - $width) / 2), ceil(($h - $height) / 2), $width, $height);
                        } elseif ($w >= $width) {
                            $xy = array(ceil(($w - $tw) / 2), 0, ceil($h / ($height / $width)), $h);
                        } elseif ($h >= $height) {
                            $xy = array(0, ceil(($h - $th) / 2), $w, ceil($w / ($width / $height)));
                        } elseif ($tw < $w) {
                            $xy = array(ceil(($w - $tw) / 2), ceil(($h - $h) / 2), $tw, $h);
                        } else {
                            $xy = array(0, ceil(($h - $th) / 2), $w, $th);
                        }

                        imageCopyResampled($new_im, $im, $xy[0], $xy[1], 0, 0, $xy[2], $xy[3], $width, $height);

                        switch ($info[2]) {
                            case 1: imageGif($new_im, $thumb); break;
                            case 2: imageJpeg($new_im, $thumb, 100); break;
                            case 3: imagePng($new_im, $thumb); break;
                        }

                        imagedestroy($im);
                        imagedestroy($new_im);

                        $data = '<div class="img-item">'
                                    .'<img id="ch_avatar" src="' . $url_path . $name . '-thumb.' . $ext . '">'
                                    //.'<a herf="#" onclick="remove_img(this); return false;"></a>' // if need button for multiple download
                                    .'<input type="hidden" name="images[]" value="' . $name . '.' . $ext . '">'
                                .'</div>';
                    } else {
                        $error = 'Не удалось загрузить файл.';
                    }
                }
            }

            $response[] = array('error' => $error, 'data'  => $data, 'img' =>'<img src="' . $url_path . $name . '-thumb.' . $ext . '">');
        }
        if($chat_backgroung) {
            $file =  $name . '.' . $ext;
            die(json_encode($this->ajaxSaveGroupChatAva($file, false)));
        }

        die(json_encode($response, JSON_UNESCAPED_UNICODE));
    }

    public function ajaxSaveGroupChatAva($file, $chatid) {

        $return['ok'] = false;
        $model = CFactory::getModel('chat');
        $dir_path = $chatid ? JPATH_ROOT.'/images/chatavatars/'.$chatid : JPATH_ROOT.'/images/chatcovers/';
        $tmp_path = JPATH_ROOT.'/tmp/';
        $filename = preg_replace("/[^a-z0-9\.-]/i", '', $file);
        $file_name = pathinfo($filename, PATHINFO_FILENAME);
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $thumb = $file_name . '-thumb.' . $file_ext;

        if(!$chatid && CFactory::getUser()->_chat_cover) {
            $oldfile = CFactory::getUser()->_chat_cover;
            $filename = preg_replace("/[^a-z0-9\.-]/i", '',  $oldfile);
            $file_name = pathinfo($filename, PATHINFO_FILENAME);
            $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
            $oldthumb = $file_name . '-thumb.' . $file_ext;
        }

        if($chatid && $model->getChat($chatid)->thumb) {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT thumb FROM #__community_chat WHERE id = '.$chatid);
            $oldthumb = $db->loadResult();
            $oldfile = preg_replace('/-thumb/', '', $oldthumb);
        }

        jimport('joomla.filesystem.file');

            if (!is_dir($dir_path)) {
                mkdir($dir_path, 0775, true);
            }

            if(!JFile::move($tmp_path.$file, $dir_path.'/'.$file) || !JFile::move($tmp_path.$thumb, $dir_path.'/'.$thumb)) {
                $return['error'] = 'Ошибка! Невозможно переместить файл. Пожалуйста, загрузите его еще раз.';
                die(json_encode($return));
            }

            $return['ok'] = $chatid ? $model->saveGroupChatAva($thumb, $chatid) : $model->saveGroupChatAva($file, $chatid);

            if($return['ok'] && $oldthumb) {
                JFile::delete($dir_path.'/'.$oldthumb);
                JFile::delete($dir_path.'/'.$oldfile);
            }

            if(!$chatid) {
                $return['file'] = $file;
                return $return;
            } else die(json_encode($return));
    }

}