import React, {useEffect, useState} from 'react';
import {
  StyleSheet,
  StatusBar,
  View,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import {observer} from 'mobx-react';
import {useTheme} from '@react-navigation/native';
import {StoreUI} from './store/StoreUI';
import {Messages} from './components/Messages';
import {HandleChat} from './components/HandleChat';
import {CreateSurveyPopup} from './components/CreateSurveyPopup';
import {ReportPopup} from './components/ReportPopup';
import {ContextMenu} from '../../components/Popups/ContextMenu';
import {SelectAvatar} from '../../widgets/SelectAvatar';
import {Popup} from '../../components/Popups';

export let Chat = observer(({navigation, route}) => {
  let [storeChat] = useState(() => new StoreUI());
  let {chatId} = route.params;

  const {colors} = useTheme();

  useEffect(() => {
    storeChat.setNavigation(navigation);
  }, [navigation, storeChat]);

  useEffect(() => {
    (async () => {
      await storeChat.init(chatId);
    })();
    return () => {
      storeChat.updateMessages();
    };
  }, [chatId, storeChat]);

  useEffect(() => {
    storeChat.setThemeColor(colors.primary);
  }, [storeChat, colors.primary]);

  return (
    <>
      <View style={styles.chat}>
        <Popup
          visible={storeChat.isShowNotifyPopup}
          title={'Жалоба'}
          description={storeChat.notifyMessage}
          titleConfirmBtn={'OK'}
          onConfirm={storeChat.onCloseNotifyPopup}
          onClose={storeChat.onCloseNotifyPopup}
          styleTitle={{marginBottom: 10}}
        />
        <ContextMenu
          visible={storeChat.showMorePopup}
          onClose={storeChat.onHideMorePopup}
          list={storeChat.dataMoreContext}
          themeColor={colors.primary}
        />
        <ContextMenu
          visible={storeChat.showMethodsUpload}
          onClose={storeChat.onHideMethodsUpload}
          list={storeChat.dataMethodsUpload}
          themeColor={colors.primary}
        />
        <CreateSurveyPopup
          visible={storeChat.showSurveyPopup}
          onClose={storeChat.onHideSurveyPopup}
          onCreateSurvey={storeChat.survey.create}
          listOptions={storeChat.survey.listOptions}
          addOption={storeChat.survey.addOption}
          changeOption={storeChat.survey.changeOption}
          deleteOption={storeChat.survey.deleteOption}
          isAcceptableCountOption={storeChat.survey.isAcceptableCountOption}
          inputTheme={{
            value: storeChat.survey.theme,
            onChange: storeChat.survey.changeTheme,
            errors: storeChat.survey.errors.theme,
          }}
          isLoading={storeChat.survey.isLoading}
        />
        <Popup
          title={'Ошибка'}
          description={storeChat.notifyMessage}
          onClose={storeChat.onHideNotify}
          onConfirm={storeChat.onHideNotify}
          visible={storeChat.showNotify}
          titleConfirmBtn={'ОК'}
        />
        <ReportPopup
          isLoading={storeChat.reportPopup.isCreatingReport}
          visible={storeChat.showReportPopup}
          onClose={storeChat.onCloseReportPopup}
          onSendReport={storeChat.onConfirmReportPopup}
          onShowImageSelect={storeChat.reportPopup.onShowImageSelect}
          onDeleteImage={storeChat.reportPopup.onDeleteImage}
          images={storeChat.reportPopup.images}
          inputTheme={{
            value: storeChat.reportPopup.theme,
            onChange: storeChat.reportPopup.onThemeChange,
            errors: storeChat.reportPopup.errors.theme,
          }}
          inputMessage={{
            value: storeChat.reportPopup.message,
            onChange: storeChat.reportPopup.onMessageChange,
            errors: storeChat.reportPopup.errors.message,
          }}
          selectComplaintReason={{
            options: storeChat.reportPopup.complaintReasons,
            onChange: storeChat.reportPopup.onSelectComplaintReason,
            value: storeChat.reportPopup.complaintReasonValue,
            title: storeChat.reportPopup.complaintReasonValue,
            errors: storeChat.reportPopup.errors.complaintReason,
          }}
          themeColor={colors.primary}
        />
        <SelectAvatar
          visible={storeChat.showSelectAvatar}
          onClose={storeChat.onHideSelectAvatar}
          onSkip={storeChat.onHideSelectAvatar}
          onConfirm={storeChat.uploadImage}
          skipText={'Закрыть'}
          title={'Выберите фото'}
        />
        <SelectAvatar
          visible={storeChat.showUpdateSkin}
          onClose={storeChat.onHideUpdateSkin}
          onSkip={storeChat.onHideUpdateSkin}
          onConfirm={storeChat.updateSkin}
          skipText={'Закрыть'}
          isLoading={storeChat.isUploadingImage}
          title={'Выберите обложку для чата'}
        />
        <SelectAvatar
          visible={storeChat.reportPopup.isShowImageSelect}
          onClose={storeChat.reportPopup.onCloseImageSelect}
          onSkip={storeChat.reportPopup.onCloseImageSelect}
          onConfirm={storeChat.reportPopup.selectImage}
          skipText={'Закрыть'}
          title={'Загрузите скриншот нарушения'}
        />
        <StatusBar
          backgroundColor={'transparent'}
          translucent={Platform.OS === 'android'}
          barStyle={'light-content'}
        />
        {storeChat.isLoading || storeChat.isUploadingFile ? (
          <ActivityIndicator
            style={styles.loader}
            color={colors.primary}
            size={'large'}
          />
        ) : (
          <KeyboardAvoidingView
            keyboardVerticalOffset={Platform.OS === 'ios' ? 92 : 0}
            enabled={true}
            behavior={Platform.OS === 'ios' ? 'padding' : ''}
            style={styles.keyboardView}>
            <Messages
              isMyChat={storeChat.storeChat.chat.isMyChat}
              chatType={storeChat.chatType}
              joined={storeChat.joined}
              onGetNext={storeChat.getNextMessages}
              list={storeChat.messages}
              openChat={{
                onPress: storeChat.openChat,
                isLoading: storeChat.isCreatedChatPrivate,
              }}
              onLike={storeChat.likeMessage}
              onDislike={storeChat.dislikeMessage}
              onSelectedOption={storeChat.changeSelectedSurveyOption}
              onAnswerSurvey={storeChat.answerSurvey}
              isAnswerSurvey={storeChat.isAnswerSurvey}
              onReport={storeChat.onShowReportPopup}
            />
            {!storeChat.isFeedbackOwner && !storeChat.isReplySent ? (
              <HandleChat
                message={storeChat.message}
                onSend={storeChat.sendMessage}
                onChangeMessage={storeChat.changeMessage}
                joined={storeChat.joined}
                onJoin={storeChat.joinChat}
                onShowMethodsUpload={storeChat.onShowMethodsUpload}
                isSendMessage={storeChat.isSendMessage}
                themeColor={colors.primary}
              />
            ) : null}
          </KeyboardAvoidingView>
        )}
      </View>
    </>
  );
});

const styles = StyleSheet.create({
  chat: {
    position: 'relative',
    height: '100%',
  },
  keyboardView: {
    height: '100%',
  },
  loader: {
    height: '100%',
    alignItems: 'center',
    justifyContent: 'center',
  },
});
