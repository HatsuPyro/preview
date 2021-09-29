import {action, observable} from 'mobx';
import {
  answerSurvey,
  createChat,
  createChatPrivate,
  dislikeMessage,
  getInfo,
  getList,
  getMessages,
  joinChat,
  leaveChat,
  likeMessage,
  sendMessage,
  updateSkin,
  createReport,
  getClaimCauses,
} from '../../services/Chats';
import {Pagination} from '../Pagination';
import {ERROR_TYPES, TYPE_CHAT} from '../../constants';

export class Chats {
  @observable data = new Pagination(this.getList, 10);
  @observable chat = new Chat(this);
  @observable error = '';

  @action.bound
  _setError(errorType, error) {
    if (errorType === ERROR_TYPES.ERROR) {
      this.error = error;
    }
  }

  @action
  async createChat(params) {
    let result = false;
    let response = await createChat(params);
    if (response.status === 200) {
      this.chat.create(response.data);
      result = true;
    } else {
      console.log(response.data);
    }
    return result;
  }

  @action
  async getList(params) {
    return await getList(params);
  }

  @action
  async getChatInfo(chatId) {
    let result = false;
    let response = await getInfo(chatId);

    if (response.status === 200) {
      result = true;
      this.chat.create(response.data);
    } else {
      console.log('Error Chats.getChatInfo()', response.data);
    }

    return result;
  }

  @action
  async getMessages(params) {
    return await getMessages(params);
  }

  @action.bound
  async sendMessage(params) {
    let result = false;
    let response = await sendMessage(params);

    if (response.status === 200) {
      result = true;
    } else {
      console.log('Error Chats.sendMessage()', response.data);
      this._setError(response.data.errorType, response.data.error);
    }

    return result;
  }

  @action
  async joinChat(chatId) {
    let result = false;
    let response = await joinChat(chatId);

    if (response.status === 200) {
      result = true;
    } else {
      console.log('Error Chats.joinChat()', response.data);
      this._setError(response.data.errorType, response.data.error);
    }

    return result;
  }

  @action
  async leaveChat(chatId) {
    let result = false;
    let response = await leaveChat(chatId);

    if (response.status === 200) {
      result = true;
    } else {
      console.log('Error Chats.leaveChat()', response.data);
    }

    return result;
  }

  @action
  async updateSkin(chatId, image) {
    let result = false;
    let response = await updateSkin(chatId, image);

    if (response.status === 200) {
      result = true;
      this.chat.image = response.data;
    } else {
      console.log('Error Chats.updateChat()', response.data);
    }

    return result;
  }

  @action
  async createChatPrivate(params) {
    let result = {
      success: false,
      chatId: null,
    };
    let response = await createChatPrivate(params);

    if (response.status === 200) {
      result.success = true;
      result.chatId = response.data.id;
    } else {
      console.log('Error Chats.createChatPrivate()', response.data);
    }

    return result;
  }

  @action
  async likeMessage(messageId) {
    let result = false;
    let response = await likeMessage(messageId);

    if (response.status === 200) {
      result = true;
    } else {
      console.log('Error Chats.likeMessage()', response.data);
    }

    return result;
  }

  @action
  async dislikeMessage(messageId) {
    let result = false;
    let response = await dislikeMessage(messageId);

    if (response.status === 200) {
      result = true;
    } else {
      console.log('Error Chats.dislikeMessage()', response.data);
    }

    return result;
  }

  async answerSurvey(params) {
    let result = {success: false, data: {}};
    let response = await answerSurvey(params);

    if (response.status === 200) {
      result = {success: true, data: response.data};
    } else {
      console.log('Error Chats.answerSurvey()', response.data);
    }

    return result;
  }

  @action
  async getClaimCauses() {
    let result = {};
    let response = await getClaimCauses();

    if (response.status === 200) {
      result.success = true;
      result.data = response.data.items;
    } else {
      result.success = false;
      result.data = [];
      console.log('Error Chats.getClaimCauses()', response.data);
    }
    return result;
  }

  @action
  async createReport(params) {
    let result = {};

    let response = await createReport(params);

    if (response.status === 200) {
      result.success = true;
      result.data = response.data;
      return result;
    } else {
      console.log('Error Chats.createReport()', response.data);
      result.success = false;
      result.data = [];
    }
    return result;
  }
}

class Chat {
  @observable id = null;
  @observable theme = '';
  @observable countMember = 0;
  @observable joined = false;
  @observable image = '';
  @observable messages = {};
  @observable isMyChat = false;
  @observable type = TYPE_CHAT.PUBLIC;
  @observable replySent = false;

  constructor(store) {
    this.store = store;
    this.messages = new Pagination(this.store.getMessages, 10, 50);
  }

  set id(value) {
    this.id = value;
  }

  set theme(value) {
    this.theme = value;
  }

  set countMember(value) {
    this.countMember = value;
  }

  set joined(value) {
    this.joined = value;
  }

  set image(value) {
    this.image = value;
  }

  set messages(value) {
    this.messages = value;
  }

  set isMyChat(value) {
    this.isMyChat = value;
  }

  @action.bound
  create(data) {
    this.id = data.id;
    this.theme = data.title;
    this.image = data.image;
    this.countMember = data.membersCount;
    this.joined = data.authUserWasJoined;
    this.isMyChat = data.authUserIsOwner;
    this.type = data.type;
    this.replySent = data.replySent;
  }
}
