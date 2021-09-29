import React, {useRef, useEffect} from 'react';
import {FlatList, StyleSheet, Platform} from 'react-native';
import {observer} from 'mobx-react';
import PropTypes from 'prop-types';
import {Message} from './Message';
import {
  BG_PRIMARY_COLOR,
  TYPE_CHAT,
  TYPE_MESSAGE,
  TYPE_REPORT,
} from '../../constants';

export let Messages = observer(
  ({
     list = [],
     onGetNext = () => {},
     openChat = {
       onPress: () => {},
       isLoading: false,
     },
     onLike = () => {},
     onDislike = () => {},
     onSelectedOption = () => {},
     onAnswerSurvey = () => {},
     joined = false,
     chatType = TYPE_CHAT.PUBLIC,
     isMyChat = false,
     isAnswerSurvey = false,
     onReport = () => {},
   }) => {
    let refMessage = useRef(null);

    useEffect(() => {
      if (refMessage) {
        refMessage.current.scrollToEnd({animated: false});
      }
    }, [refMessage]);

    return (
      <FlatList
        onEndReached={onGetNext}
        onEndReachedThreshold={0.1}
        inverted={true}
        ref={refMessage}
        scrollEventThrottle={16}
        keyExtractor={(item) => String(item.id)}
        contentContainerStyle={styles.container}
        data={list}
        renderItem={({item: message}) => (
          <Message
            onReport={() => onReport(TYPE_REPORT.MESSAGE, message.id)}
            isMyChat={isMyChat}
            chatType={chatType}
            joined={joined}
            username={message.username}
            text={message.text}
            itsMe={message.itsMe}
            countLikes={message.countLikes}
            countDislikes={message.countDislikes}
            date={message.date}
            typeMessage={message.typeMessage}
            image={message.image}
            openChat={{
              isLoading: openChat.isLoading,
              onPress: () => openChat.onPress(message.memberId, message.id),
            }}
            urlFile={message.urlFile}
            onLike={() => onLike(message.id)}
            onDislike={() => onDislike(message.id)}
            hasLike={message.hasLike}
            hasDislike={message.hasDislike}
            fileSize={message.fileSize}
            fileName={message.fileName}
            isSurvey={message.typeMessage === TYPE_MESSAGE.SURVEY}
            surveyData={{
              isAnswerSurvey: isAnswerSurvey,
              options: message.surveyData,
              theme: message.surveyTheme,
              countParticipants: message.surveyCountParticipants,
              onSelectedOption: (value) => onSelectedOption(message.id, value),
              surveySelectedOption: message.surveySelectedOption,
              isFulfilled: message.surveyFulfilled,
              onAnswerSurvey: () =>
                onAnswerSurvey(message.id, message.surveySelectedOption),
            }}
          />
        )}
      />
    );
  },
);

Messages.propTypes = {
  list: PropTypes.array,
  onGetNext: PropTypes.func,
  openChat: PropTypes.shape({
    onPress: PropTypes.func,
    isLoading: PropTypes.bool,
  }),
  onLike: PropTypes.func,
  onDislike: PropTypes.func,
  onSelectedOption: PropTypes.func,
  onAnswerSurvey: PropTypes.func,
  joined: PropTypes.bool,
  chatType: PropTypes.string,
  onReport: PropTypes.func,
  isMyChat: PropTypes.bool,
  isAnswerSurvey: PropTypes.bool,
};

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 16,
    paddingBottom: Platform.OS === 'android' ? 20 : 40,
    backgroundColor: BG_PRIMARY_COLOR,
    paddingTop: 104,
    flexDirection: 'column-reverse',
  },
});
