import axios from 'axios';
import {API_URLS, RANGE_VALIDATE_STATUS, TYPE_MESSAGE} from '../../constants';
import {setFormData} from '../helpers';

/**
 *
 * @typedef {Object} params
 * @property {string} title* - Тема чата
 * @property {string} type* - Тип чата [PRIVATE, PUBLIC]
 * @property {file} image - Обложка чата (картинка)
 * @property {string} firstMessage*  - Первое сообщение чата
 * @property {array} tags - Тэги чата (интересы)
 * @property {string} canSee* - Кто может видеть чат в списке
 * @property {string} blackListUsers  - Кто может видеть чат в списке
 * @property {string} maxCountMembers*  - Количество участников
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function createChat(params) {
  return await axios.post(API_URLS.CHAT_CREATE, setFormData(params), {
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });
}

/**
 *
 * @param type* {string} - Тип чата [PRIVATE, PUBLIC]
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function getList(params) {
  return await axios.get(API_URLS.CHATS_LIST, {
    params,
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 *
 * @param chatId - id чата
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function getInfo(chatId) {
  return await axios.get(API_URLS.CHAT_INFO(chatId), {
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 *
 * @typedef {Object}
 * @property chatId - id чата
 * @property lastId - Последний id на странице, если нет то возвращаются первые
 * @property count - Количество возвращаемых записей, если не передан, то по умолчанию
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function getMessages(params) {
  return await axios.get(API_URLS.CHAT_MESSAGES(params.chatId), {
    params,
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 *
 * @typedef {Object} params
 * @property chatId - id чата
 * @property type - тип сообщения
 * @property messageText - текст сообщения
 * @property file - прикрепляемый файл
 *
 * @returns {Promise<void>}
 */
export async function sendMessage(
  params = {
    chatId: null,
    messageText: '',
    type: TYPE_MESSAGE.TEXT,
    file: undefined,
  },
) {
  return await axios.post(
    API_URLS.CHAT_MESSAGES_ADD(params.chatId),
    setFormData(params),
    {
      validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    },
  );
}

/**
 * Метод присоединения к чату
 *
 * @param chatId - id чата
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function joinChat(chatId) {
  return await axios.post(
    API_URLS.CHAT_JOIN(chatId),
    {},
    {
      validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
    },
  );
}

/**
 * Метод отсоединения от чата
 *
 * @param chatId - id чата
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function leaveChat(chatId) {
  return await axios.post(
    API_URLS.CHAT_LEAVE(chatId),
    {},
    {
      validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
    },
  );
}

/**
 * Метод обновления обложки чата
 *
 * @param chatId - id чата
 * @param image - изображение
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function updateSkin(chatId, image) {
  return await axios.post(
    API_URLS.CHAT_UPDATE_SKIN(chatId),
    setFormData({image}),
    {
      validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
    },
  );
}

/**
 * Метод создаеия приватного чата с конкретным юзером
 *
 * @typedef {Object} params
 * @property chatId - id чата
 * @property messageId - id сообщения
 * @property memberId - ID юзера, с которым создается чат
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function createChatPrivate(params) {
  return await axios.post(API_URLS.CHAT_CREATE_PRIVATE, params, {
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 * Метод постановки лайка сообщению
 *
 * @param messageId {number} - id сообщеия
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function likeMessage(messageId) {
  return await axios.post(
    API_URLS.CHAT_LIKE(messageId),
    {},
    {
      validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
    },
  );
}

/**
 * Метод постановки дизлайка сообщению
 *
 * @param messageId {number} - id сообщеия
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function dislikeMessage(messageId) {
  return await axios.post(
    API_URLS.CHAT_DISLIKE(messageId),
    {},
    {
      validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
    },
  );
}

/**
 * Метод создания чата по фидбеку
 *
 * @param markId {number} - id оценки
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function createFeedback(markId) {
  return await axios.post(
    API_URLS.CHAT_CREATE_FEEDBACK,
    {markId},
    {
      validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
    },
  );
}

/**
 * Метод создания жалобы
 *
 * @typedef {Object} params - параметры жалобы
 * @property {string} subject - параметры жалобы
 * @property {number} subjectId - параметры жалобы
 * @returns {Promise<AxiosResponse<any>>}
 */

export async function createReport(params) {
  return await axios.post(API_URLS.CHAT_CREATE_REPORT, params, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 * Метод создания опроса в чате
 *
 * @typedef {Object} params
 * @property {number} chatId  - id чата
 * @property {string} pollTheme - Тема опроса
 * @property {array} pollOptions - Список опций
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function createSurvey(params) {
  return await axios.post(API_URLS.CREATE_SURVEY, params, {
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 * Метод получения информации об опросе
 *
 * @param chatId  {number} - id чата
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function getInfoSurvey(chatId) {
  return await axios.get(API_URLS.INFO_SURVEY(chatId), {
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 * Метод получения списка причин жалоб
 *
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function getClaimCauses() {
  return await axios.get(API_URLS.CHAT_CLAIM_CAUSES, {
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}

/**
 * Метод выбора варианта ответа в опросе
 *
 * @typedef {Object}
 * @property {number} chatId   - id чата
 * @property {number} messageId    - id сообщения
 * @property {number} chosenOptionNumber  - Выбранная опция опроса
 * @returns {Promise<AxiosResponse<any>>}
 */
export async function answerSurvey(params) {
  return await axios.post(API_URLS.ANSWER_SURVEY(params.chatId), params, {
    validateStatus: (status) => RANGE_VALIDATE_STATUS(status),
  });
}
