import {action, observable, computed} from 'mobx';
import Validator from '../../helpers/Validator';

class ReportPopup {
  @observable isShowImageSelect = false;
  @observable images = [];
  @observable theme = '';
  @observable message = '';
  @observable complaintReasons = [];
  @observable complaintReasonValue = '';
  @observable complaintReasonId = 0;
  @observable isCreatingReport = false;
  @observable errors = {
    theme: [],
    message: [],
    complaintReason: [],
  };

  _maxImageCount = 5;
  messageId = 0;
  chatId = 0;
  subject = TYPE_REPORT.MESSAGE;
  _validatorRules = {
    theme: {required: true, isSymbols: true},
    message: {required: true},
    complaintReason: {required: true},
  };

  constructor(storeChats) {
    this.storeChats = storeChats;
    this._validator = new Validator(this._validatorRules);
    this.getClaimCauses();
  }

  @action
  async getClaimCauses() {
    let result = await this.storeChats.getClaimCauses();

    if (result.data) {
      this.complaintReasons = result.data.map((item) => {
        return {
          id: item.id,
          value: item.name,
          title: item.name,
        };
      });
    } else {
      return [];
    }
  }

  _createFormData() {
    let formData = new FormData();

    let images = this.images.map((item) => {
      return {
        name: item.name,
        type: item.type,
        uri: item.uri,
      };
    });

    formData.append('entity', this.subject);
    formData.append('chatId', this.chatId);
    if (this.subject === TYPE_REPORT.MESSAGE) {
      formData.append('messageId', this.messageId);
    }
    formData.append('claimTheme', this.theme);
    formData.append('claimText', this.message);
    formData.append('claimCauseId', this.complaintReasonId);
    images.forEach((item) => formData.append('file[]', item));

    return formData;
  }

  @action.bound
  async createReport() {
    let result = {result: false, message: 'Не удалось отправить жалобу'};
    this.isCreatingReport = true;
    this.errors = {};

    let validation = this._validator.checkAll({
      theme: String(this.theme).trim(),
      message: String(this.message).trim(),
      complaintReason: String(this.complaintReasonValue).trim(),
    });

    if (validation.passed) {
      let formData = this._createFormData();
      let response = await this.storeChats.createReport(formData);
      if (response.success) {
        result = {result: true, message: 'Ваша жалоба успешно отправлена'};
        this.clear();
      }
    } else {
      this.errors = validation.errors;
    }

    this.isCreatingReport = false;
    return result;
  }

  @action.bound
  onShowImageSelect() {
    this.storeChats.onCloseReportPopup();
    this.isShowImageSelect = true;
  }

  @action.bound
  onCloseImageSelect() {
    this.storeChats.onShowReportPopup(this.subject, this.messageId);
    this.isShowImageSelect = false;
  }

  @action.bound
  onThemeChange(value) {
    this.theme = value;
    let validation = this._validator.check('theme', this.theme);
    this.errors.theme = validation.errors;
  }

  @action.bound
  onMessageChange(value) {
    this.message = value;
    let validation = this._validator.check('message', this.message);
    this.errors.message = validation.errors;
  }

  @action.bound
  onSelectComplaintReason(value) {
    this.complaintReasonValue = value.title;
    this.complaintReasonId = value.id;
    let validation = this._validator.check(
      'complaintReason',
      this.complaintReasonValue,
    );
    this.errors.complaintReason = validation.errors;
  }

  @action.bound
  async selectImage(image) {
    let loadedImage = {
      name: image?.filename,
      type: image?.type,
      uri: image?.uri,
    };

    if (image) {
      loadedImage.id = `${Math.random()}-${loadedImage?.name}`;
    }

    let isExists = this._imageAlreadyExists(loadedImage.name);

    if (!isExists && this.images.length < this._maxImageCount) {
      this.images.push(loadedImage);
    }

    this.onCloseImageSelect();
  }

  @action.bound
  onDeleteImage(id) {
    this.images = this.images.filter((item) => item.id !== id);
  }

  _imageAlreadyExists(name) {
    return this.images.find((item) => item.name === name);
  }

  @action
  clear() {
    this.images = [];
    this.theme = '';
    this.message = '';
    this.complaintReasonValue = '';
    this.errors = {};
    this.subject = '';
    this.subjectId = 0;
  }

  setMessageId(messageId) {
    this.messageId = messageId;
  }

  setChatId(chatId) {
    this.chatId = chatId;
  }

  setSubject(subject) {
    this.subject = subject;
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
