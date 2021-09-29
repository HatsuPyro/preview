import {action, observable, computed} from 'mobx';
import Validator from '../../helpers/Validator';

class Survey {
  @observable theme = '';
  @observable listOptions = [];
  @observable isLoading = false;
  @observable errors = {
    theme: [],
  };

  constructor(store) {
    this.store = store;
    this._createDefaultOptions();
    this._validator = new Validator({
      theme: {
        required: true,
        isSymbols: true,
      },
      option: {required: true},
    });
  }

  @computed
  get isAcceptableCountOption() {
    return this.listOptions.length < 15;
  }

  @action.bound
  async create() {
    this.isLoading = true;
    let validation = this._validationFields();
    if (validation) {
      let pollOptions = this.listOptions
        .filter((option) => !!option.value.length)
        .map((option, optionIndex) => ({
          name: option.value,
          number: String(optionIndex + 1),
        }));
      await this.store.createSurvey({
        chatId: this.store.id,
        pollTheme: this.theme,
        pollOptions,
        type: TYPE_MESSAGE.SURVEY,
      });
      await this.store.getNewMessages();
      this._clear();
    }
    this.isLoading = false;
  }

  @action.bound
  changeTheme(value) {
    this.theme = value;
    let validation = this._validator.check('theme', this.theme);
    this.errors.theme = validation.errors;
  }

  @action
  _createDefaultOptions() {
    this.listOptions.push(this._createOption(1, false));
    this.listOptions.push(this._createOption(2, false));
  }

  @action.bound
  _createOption(number, showDeleteButton) {
    return observable({
      id: number + Math.random(),
      value: '',
      label: `Вариант ${number}`,
      showDeleteButton,
      number,
      errors: [],
    });
  }

  @action.bound
  changeOption(state) {
    return action((value) => {
      state.value = value;
      if (!state.showDeleteButton) {
        let validation = this._validator.check('option', state.value);
        state.errors = validation.errors;
      }
    });
  }

  @action.bound
  addOption() {
    // не должно быть больше 15 опций
    if (this.isAcceptableCountOption) {
      let _nextNumberOption =
        this.listOptions[this.listOptions.length - 1].number + 1;
      this.listOptions.push(this._createOption(_nextNumberOption, true));
    }
  }

  @action.bound
  deleteOption(id) {
    this.listOptions = this.listOptions
      .filter((option) => option.id !== id)
      .map((option, optionIndex) => {
        option.label = `Вариант ${optionIndex + 1}`;
        option.number = optionIndex + 1;
        return option;
      });
  }

  _validationFields() {
    let result = false;
    let validationTheme = this._validator.check('theme', this.theme);
    let validationOptionOne = this._validator.check(
      'option',
      this.listOptions[0].value,
    );
    let validationOptionTwo = this._validator.check(
      'option',
      this.listOptions[1].value,
    );
    let validity =
      validationTheme.passed &&
      validationOptionOne.passed &&
      validationOptionTwo.passed;

    if (validity) {
      result = true;
    } else {
      this.errors.theme = validationTheme.errors;
      this.listOptions[0].errors = validationOptionOne.errors;
      this.listOptions[1].errors = validationOptionTwo.errors;
    }
    return result;
  }

  @action.bound
  _clear() {
    this.theme = '';
    this.listOptions = [];
    this._createDefaultOptions();
    this.isLoading = false;
    this.errors = {
      theme: [],
    };
  }
}
