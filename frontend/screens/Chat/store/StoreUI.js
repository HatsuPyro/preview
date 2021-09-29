import {action, autorun, computed, observable} from 'mobx';
import DocumentPicker from 'react-native-document-picker';
import {Chats} from '../../Stores/Chats';
import {settingsNavigation} from '../helpers/settingsNavigation';
import {
  DOMAIN,
  THEME_COLOR,
  TYPE_CHAT,
  TYPE_MESSAGE,
  TYPE_REPORT,
} from '../../constants';
import {formattingDate} from '../../helpers/formattingDate';

export class StoreUI {
  @observable id = null;
  @observable showMorePopup = false;
  @observable showMethodsUpload = false;
  @observable showSurveyPopup = false;
  @observable showSelectAvatar = false;
  @observable showUpdateSkin = false;
  @observable showNotify = false;
  @observable navigation = {};
  @observable isLoading = false;
  @observable isUpdateMessages = false;
  @observable isUploadingFile = false;
  @observable isUploadingImage = false;
  @observable isSendMessage = false;
  @observable isCreatedChatPrivate = false;
  @observable isAnswerSurvey = false;
  @observable message = '';
  @observable uploadedImage = undefined;
  @observable uploadedFile = undefined;
  @observable chatType = TYPE_CHAT.PUBLIC;
  @observable showReportPopup = false;
  @observable isShowNotifyPopup = false;
  @observable notifyMessage = '';
  @observable messages = [];
  @observable themeColor = THEME_COLOR;
  _typeMessage = TYPE_MESSAGE.TEXT;

  dataMethodsUpload = [
    {
      title: 'Выбрать фото',
      on: this.onShowSelectAvatar,
    },
    {
      title: 'Выбрать файл',
      on: this.openFileSystem,
    },
  ];

  constructor() {
    this.storeChat = new Chats();
    this.survey = new Survey(this);
    this.reportPopup = new ReportPopup(this);
  }

  @action.bound
  async init(chatId) {
    this.isLoading = true;
    await Promise.all([this.getInfo(chatId), this.getMessages(chatId)]);
    this.isLoading = false;
  }

  setSettingsNavigation = autorun(() => {
    if (Object.keys(this.navigation).length) {
      settingsNavigation(
        this.navigation,
        this.onShowMorePopup,
        {
          image: this.image,
          countMember: this.countMember,
          theme: this.theme,
        },
        this.isLoading,
        this.themeColor,
      );
    }
  });

  updateMessages = autorun(async () => await this.getNewMessages(), {
    scheduler: (run) => {
      setInterval(run, 15000);
    },
  });

  @action.bound
  setThemeColor(color) {
    this.themeColor = color;
  }
  @action.bound
  setNavigation(navigation) {
    this.navigation = navigation;
  }

  @computed
  get isFeedbackOwner() {
    return this.chatType === TYPE_CHAT.FEEDBACK && this.storeChat.chat.isMyChat;
  }

  @computed
  get isReplySent() {
    return (
      this.storeChat.chat.replySent && this.chatType === TYPE_CHAT.FEEDBACK
    );
  }

  _setReplySent() {
    if (this.chatType === TYPE_CHAT.FEEDBACK) {
      this.storeChat.chat.replySent = true;
    }
  }

  @action.bound
  async getInfo(chatId) {
    this.id = chatId;
    await this.storeChat.getChatInfo(chatId);
    this.chatType = this.storeChat.chat.type;
  }

  @action.bound
  async getMessages(chatId) {
    await this.storeChat.chat.messages.getCurrent({chatId});
    this._setMessages();
  }

  @action.bound
  async getNextMessages() {
    let _isLoading = this.storeChat.chat.messages.loading;
    let _isLoadingNext = this.storeChat.chat.messages.loadingNext;
    if (!_isLoading && !_isLoadingNext) {
      this.isUpdateMessages = true;
      await this.storeChat.chat.messages.getNext({chatId: this.id, count: 5});
      this._setMessages();
      this.isUpdateMessages = false;
    }
  }

  @action.bound
  async getNewMessages() {
    let isNew = await this.storeChat.chat.messages.checkNew({chatId: this.id});
    if (isNew) {
      await this.storeChat.chat.messages.getNew({chatId: this.id});
      this._setMessages();
    }
  }

  @action.bound
  async openChat(memberId, messageId) {
    this.isCreatedChatPrivate = true;
    let result = await this.storeChat.createChatPrivate({
      chatId: this.id,
      messageId,
      memberId,
    });
    if (result.success) {
      this.navigation.push('Chat', {chatId: result.chatId});
    }
    this.isCreatedChatPrivate = false;
  }

  @action.bound
  async sendMessage() {
    this.isSendMessage = true;
    this._typeMessage = TYPE_MESSAGE.TEXT;
    if (this.message.length) {
      let result = await this.storeChat.sendMessage({
        chatId: this.id,
        messageText: this.message,
        type: this._typeMessage,
      });
      if (result) {
        await this.getNewMessages();
        this.message = '';
        this._setReplySent();
      } else {
        this._setNotify();
      }
    }
    this.isSendMessage = false;
  }

  @action.bound
  async _sendImage() {
    await this.storeChat.sendMessage({
      chatId: this.id,
      messageText: 'Изображение',
      type: this._typeMessage,
      file: this.uploadedImage,
    });
  }

  @action.bound
  async _sendFile() {
    await this.storeChat.sendMessage({
      chatId: this.id,
      messageText: 'Файл',
      type: this._typeMessage,
      file: this.uploadedFile,
    });
  }

  @action.bound
  async uploadImage(image) {
    this.isUploadingFile = true;
    this._typeMessage = TYPE_MESSAGE.IMAGE;
    this.selectImage(image);
    await this._sendImage();
    await this.getNewMessages();
    this.onHideSelectAvatar();
    this.isUploadingFile = false;
  }

  @action.bound
  async uploadFile(file) {
    this.isUploadingFile = true;
    if (file.success) {
      this._typeMessage = TYPE_MESSAGE.FILE;
      this.selectFile(file);
      this.onHideMethodsUpload();
      await this._sendFile();
      await this.getNewMessages();
    } else {
      this.onHideMethodsUpload();
    }
    this.isUploadingFile = false;
  }

  @action.bound
  async joinChat() {
    let result = await this.storeChat.joinChat(this.id);
    if (result) {
      this.storeChat.chat.joined = true;
      this.storeChat.chat.countMember++;
    } else {
      this._setNotify();
    }
  }

  @action.bound
  async leaveChat() {
    let result = await this.storeChat.leaveChat(this.id);
    if (result) {
      this.storeChat.chat.joined = false;
      this.onHideMorePopup();
      this.navigation.goBack();
    }
  }

  @action.bound
  async updateSkin(image) {
    this.isUploadingImage = true;
    this.selectImage(image);
    let result = await this.storeChat.updateSkin(this.id, this.uploadedImage);
    if (result) {
      this.onHideUpdateSkin();
    }
    this.isUploadingImage = false;
  }

  @action.bound
  async likeMessage(messageId) {
    let _list = this.storeChat.chat.messages.list;
    let result = await this.storeChat.likeMessage(messageId);
    if (result) {
      _list.forEach((message) => {
        if (message.id === messageId) {
          message.userLikeExists = true;
          message.countLikes++;
          if (message.userDislikeExists) {
            message.userDislikeExists = false;
            message.countDislikes--;
          }
        }
        return message;
      });
    }
    this._setMessages();
  }

  @action.bound
  async dislikeMessage(messageId) {
    let _list = this.storeChat.chat.messages.list;
    let result = await this.storeChat.dislikeMessage(messageId);
    if (result) {
      _list.forEach((message) => {
        if (message.id === messageId) {
          message.countDislikes++;
          message.userDislikeExists = true;
          if (message.userLikeExists) {
            message.userLikeExists = false;
            message.countLikes--;
          }
        }
        return message;
      });
    }
    this._setMessages();
  }

  async createSurvey(params) {
    let result = await this.storeChat.sendMessage(params);
    if (result) {
      this.onHideSurveyPopup();
    }
  }

  @action.bound
  async answerSurvey(messageId, selectedOption) {
    this.isAnswerSurvey = true;
    let result = await this.storeChat.answerSurvey({
      messageId,
      chatId: this.id,
      chosenOptionNumber: selectedOption,
    });
    if (result.success) {
      await this.getMessages(this.id);
    }
    this.isAnswerSurvey = false;
  }

  @action.bound
  async getClaimCauses() {
    return await this.storeChat.getClaimCauses();
  }

  @action.bound
  async createReport(params) {
    return await this.storeChat.createReport(params);
  }

  @computed
  get image() {
    let result = '';
    let _image = this.storeChat.chat.image;
    if (_image.length) {
      result = DOMAIN + _image;
    }
    return result;
  }

  @computed
  get theme() {
    return this.storeChat.chat.theme || '';
  }

  @computed
  get countMember() {
    return this.storeChat.chat.countMember || 0;
  }

  @computed
  get joined() {
    return this.storeChat.chat.joined || false;
  }

  _setMessages() {
    let _list = this.storeChat.chat.messages.list.concat().reverse();
    this.messages = this._formattingMessages(_list) || [];
  }

  @computed
  get isMyChat() {
    return this.storeChat.chat.isMyChat || false;
  }

  @computed
  get dataMoreContext() {
    let result = [
      {
        title: 'Присоединиться к чату',
        on: this.joinChat,
      },
    ];
    if (this.joined) {
      result = [
        {
          title: 'Создать опрос',
          on: this.onShowSurveyPopup,
        },
        {
          title: 'Пожаловаться',
          on: this._showChatReportPopup,
        },
        {
          title: 'Покинуть чат',
          on: this.leaveChat,
        },
      ];
      if (this.isMyChat) {
        result = [
          {
            title: 'Изменить обложку чата',
            on: this.onShowUpdateSkin,
          },
          {
            title: 'Создать опрос',
            on: this.onShowSurveyPopup,
          },
          {
            title: 'Покинуть чат',
            on: this.leaveChat,
          },
        ];
      }
      if (this.chatType === TYPE_CHAT.FEEDBACK) {
        result = [
          {
            title: 'Покинуть чат',
            on: this.leaveChat,
          },
        ];
      }
    }
    return result;
  }

  @action.bound
  _showChatReportPopup() {
    this.onHideMorePopup();
    this.onShowReportPopup(TYPE_REPORT.CHAT);
  }

  @action.bound
  set navigation(value) {
    this.navigation = value;
  }

  @action.bound
  changeMessage(value) {
    this.message = value;
  }

  @action.bound
  selectImage(image) {
    this.uploadedImage = {
      name: image.filename,
      type: image.type,
      uri: image.uri,
    };
  }

  @action.bound
  selectFile(file) {
    let name = file.name;
    let type = file.type;
    let uri = file.uri;
    this.uploadedFile = {
      name,
      type,
      uri,
    };
  }

  @action.bound
  async openFileSystem() {
    let result;
    try {
      result = await DocumentPicker.pick({
        type: [DocumentPicker.types.allFiles],
      });
      result = {
        success: true,
        ...result,
      };
    } catch (e) {
      if (DocumentPicker.isCancel(e)) {
        result = {
          success: false,
        };
      } else {
        throw e;
      }
    }
    await this.uploadFile(result);
  }

  @action.bound
  _setNotify() {
    this.notifyMessage = this.storeChat.error;
    this.onShowNotify();
  }

  @action.bound
  changeSelectedSurveyOption(messageId, value) {
    this.messages = this.messages.map((message) => {
      if (messageId === message.id) {
        message.surveySelectedOption = value;
      }
      return message;
    });
  }

  @action.bound
  _formattingMessages(list) {
    let result = [];
    result = list.map((message) => {
      let _image =
        message.sourceImage && /^(\/storage.+)$/gim.test(message.sourceImage)
          ? DOMAIN + message.sourceImage
          : message.sourceImage;
      let survey = [];

      if (message.messageType === TYPE_MESSAGE.SURVEY) {
        survey = this._formattingSurvey(message.pollData);
      }

      return {
        id: message?.id,
        username: message?.username,
        text: message?.text || '',
        countLikes: message?.countLikes || 0,
        countDislikes: message?.countDislikes || 0,
        image: _image,
        itsMe: message?.isMyMessage,
        date: formattingDate(message?.date),
        typeMessage: message?.messageType,
        memberId: message?.user_id,
        urlFile: DOMAIN + message?.sourceFile,
        hasLike: message?.userLikeExists,
        hasDislike: message?.userDislikeExists,
        fileSize: message?.sourceFileSize || '',
        fileName: message?.sourceFileName || '',
        surveyData: survey,
        surveyTheme: message?.pollTheme || '',
        surveyCountParticipants: message?.totalCountRespondents || 0,
        surveyFulfilled: message?.userIsPollRespondent || false,
        surveySelectedOption: 0,
      };
    });
    return observable(result);
  }

  _formattingSurvey(data) {
    let result = [];
    result = data.map((option, optionIndex) =>
      observable({
        percent: option.averagePercentage,
        countPeopleVoted: option.countOfChose,
        title: `${optionIndex + 1}. ${option.optionName}`,
        value: option.optionNumber,
      }),
    );

    return result;
  }

  @action.bound
  onShowMorePopup() {
    this.showMorePopup = true;
  }

  @action.bound
  onHideMorePopup() {
    this.showMorePopup = false;
  }

  @action.bound
  onShowSurveyPopup() {
    this.showSurveyPopup = true;
    this.onHideMorePopup();
  }

  @action.bound
  onHideSurveyPopup() {
    this.showSurveyPopup = false;
  }

  @action.bound
  onShowSelectAvatar() {
    this.onHideMethodsUpload();
    this.showSelectAvatar = true;
  }

  @action.bound
  onHideSelectAvatar() {
    this.showSelectAvatar = false;
  }

  @action.bound
  onShowMethodsUpload() {
    this.showMethodsUpload = true;
  }

  @action.bound
  onHideMethodsUpload() {
    this.showMethodsUpload = false;
  }

  @action.bound
  onShowUpdateSkin() {
    this.onHideMorePopup();
    this.showUpdateSkin = true;
  }

  @action.bound
  onHideUpdateSkin() {
    this.showUpdateSkin = false;
  }

  @action.bound
  onShowReportPopup(type, messageId) {
    this.reportPopup.setChatId(this.id);
    if (messageId) {
      this.reportPopup.setMessageId(messageId);
    }
    this.reportPopup.setSubject(type);
    this.showReportPopup = true;
  }

  @action.bound
  onCloseReportPopup() {
    this.showReportPopup = false;
  }

  @action.bound
  async onConfirmReportPopup() {
    let response = await this.reportPopup.createReport();
    this.notifyMessage = response.message;
    this.onCloseReportPopup();
    this.onShowNotifyPopup();
  }

  @action
  onShowNotifyPopup() {
    this.isShowNotifyPopup = true;
  }

  @action.bound
  onCloseNotifyPopup() {
    this.isShowNotifyPopup = false;
  }

  @action.bound
  onShowNotify() {
    this.showNotify = true;
  }

  @action.bound
  onHideNotify() {
    this.showNotify = false;
  }
}
