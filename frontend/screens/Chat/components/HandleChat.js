import React, {useRef, useCallback} from 'react';
import {
  View,
  StyleSheet,
  Image,
  Pressable,
  Text,
  Platform,
  ActivityIndicator,
  Animated,
  Keyboard,
} from 'react-native';
import PropTypes from 'prop-types';
import {Input} from '../../components/Input';
import {
  ACCENT_COLOR,
  BG_PRIMARY_COLOR,
  FAMILY_BOLD,
  THEME_COLOR,
  WIDTH_ELEMENT,
  WINDOW_WIDTH,
} from '../../constants';
import {ButtonControl} from '../../components/Button/ButtonControl';
import {BoxShadow} from 'react-native-shadow';
import Icon from '../../components/Icon';

const boxShadowSettings = {
  width: WINDOW_WIDTH,
  height: 92,
  color: '#cdd2d9',
  border: 16,
  radius: 8,
  opacity: 0.5,
  x: 0,
  y: 0,
};

function ControlChat({
                       onSend = () => {},
                       message = '',
                       onChangeMessage = () => {},
                       onShowMethodsUpload = () => {},
                       isSendMessage = false,
                     }) {
  return (
    <View style={styles.container}>
      <View style={styles.inputWrapper}>
        <Input
          theme={'inset'}
          placeholder={'Ваше сообщение'}
          width={WIDTH_ELEMENT - 48}
          stylesInput={[
            styles.input,
            Platform.OS === 'ios' ? {paddingTop: 12} : {},
          ]}
          placeholderTextColor={'#ccc'}
          borderRadius={16}
          value={message}
          onChange={onChangeMessage}
          multiline={true}
        />
        <View style={styles.buttons}>
          <Pressable onPress={onShowMethodsUpload}>
            <Icon
              name={'attach'}
              width={'24'}
              height={'24'}
              viewBox={'0 0 24 24'}
            />
          </Pressable>
          {/*<Pressable> @TODO не идёт в данный этап*/}
          {/*  <Icon*/}
          {/*    name={'smile'}*/}
          {/*    width={'24'}*/}
          {/*    height={'24'}*/}
          {/*    viewBox={'0 0 24 24'}*/}
          {/*  />*/}
          {/*</Pressable>*/}
        </View>
      </View>
      <ButtonControl
        onPress={onSend}
        height={40}
        width={40}
        icon={
          isSendMessage ? (
            <ActivityIndicator color={ACCENT_COLOR} size={'small'} />
          ) : (
            <Image
              source={require('../../assets/images/icons/send.png')}
              style={styles.image}
            />
          )
        }
      />
    </View>
  );
}

function ButtonJoin({onPress = () => {}, themeColor = THEME_COLOR}) {
  return (
    <Pressable
      style={[styles.btnJoin, {backgroundColor: themeColor}]}
      onPress={onPress}>
      <Text style={styles.textJoin}>Присоединиться к чату</Text>
    </Pressable>
  );
}

export function HandleChat({
                             onSend = () => {},
                             message = '',
                             onChangeMessage = () => {},
                             joined = false,
                             onJoin = () => {},
                             onShowMethodsUpload = () => {},
                             isSendMessage = false,
                             themeColor = THEME_COLOR,
                           }) {
  let transformYInput = useRef(new Animated.Value(0)).current;

  let _keyboardWillShow = useCallback(
    (event) => {
      let height = event.endCoordinates.height;
      Animated.timing(transformYInput, {
        toValue: -height,
        useNativeDriver: true,
        duration: 50,
      }).start();
    },
    [transformYInput],
  );
  let _keyboardWillHide = useCallback(() => {
    Animated.timing(transformYInput, {
      toValue: 0,
      useNativeDriver: true,
      duration: 50,
    }).start();
  }, [transformYInput]);

  React.useEffect(() => {
    Keyboard.addListener('keyboardWillShow', _keyboardWillShow);
    Keyboard.addListener('keyboardWillHide', _keyboardWillHide);
    return () => {
      Keyboard.removeListener('keyboardWillShow', _keyboardWillShow);
      Keyboard.removeListener('keyboardWillHide', _keyboardWillHide);
    };
  }, [_keyboardWillShow, _keyboardWillHide]);

  return (
    <Animated.View
      style={[
        styles.handleChatBox,
        {transform: [{translateY: transformYInput}]},
      ]}>
      <BoxShadow style={styles.boxShadowSettings} setting={boxShadowSettings}>
        {joined ? (
          <ControlChat
            onSend={onSend}
            message={message}
            onChangeMessage={onChangeMessage}
            onShowMethodsUpload={onShowMethodsUpload}
            isSendMessage={isSendMessage}
          />
        ) : (
          <ButtonJoin onPress={onJoin} themeColor={themeColor} />
        )}
      </BoxShadow>
    </Animated.View>
  );
}

const primaryPropTypes = {
  onSend: PropTypes.func,
  onChangeMessage: PropTypes.func,
  onShowMethodsUpload: PropTypes.func,
  message: PropTypes.string,
  isSendMessage: PropTypes.bool,
};

HandleChat.propTypes = {
  ...primaryPropTypes,
  joined: PropTypes.bool,
  onJoin: PropTypes.func,
  themeColor: PropTypes.string,
};
ControlChat.propTypes = primaryPropTypes;

const styles = StyleSheet.create({
  handleChatBox: {
    height: 92,
    zIndex: 100,
    position: 'absolute',
    bottom: 0,
    left: 0,
    width: '100%',
  },
  container: {
    backgroundColor: BG_PRIMARY_COLOR,
    height: 92,
    width: '100%',
    flexDirection: 'row',
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    paddingHorizontal: 18,
    paddingTop: 16,
  },
  image: {
    width: 32,
    height: 32,
    resizeMode: 'cover',
  },
  input: {
    paddingLeft: 16,
    paddingRight: 80,
  },
  inputWrapper: {
    position: 'relative',
    marginRight: 4,
  },
  shadow: {
    height: 16,
    position: 'absolute',
    top: -16,
    left: 0,
    width: WINDOW_WIDTH,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
  },
  buttons: {
    position: 'absolute',
    right: 16,
    top: 8,
    flexDirection: 'row',
    alignItems: 'center',
  },
  btnJoin: {
    height: 92,
    width: '100%',
    flexDirection: 'row',
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
  },
  textJoin: {
    color: 'white',
    fontFamily: FAMILY_BOLD,
    fontSize: 16,
    lineHeight: 24,
    textAlign: 'center',
  },
});
