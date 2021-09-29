import React, {useMemo} from 'react';
import {
  StyleSheet,
  View,
  Image,
  Pressable,
  Text,
  ScrollView,
} from 'react-native';
import PropTypes from 'prop-types';
import {observer} from 'mobx-react';
import {Popup} from '../../../components/Popups';
import {Input} from '../../../components/Input';
import {Textarea} from '../../../components/Textarea';
import {
  ACCENT_COLOR,
  BG_PRIMARY_COLOR,
  FAMILY_MEDIUM,
  TEXT_ACCENT,
  THEME_COLOR,
  WIDTH_ELEMENT,
} from '../../../constants';
import {Select} from '../../../components/Select';
import Icon from '../../../components/Icon';

export const ReportPopup = observer(
  ({
     visible = false,
     onClose = () => {},
     onSendReport = () => {},
     onShowImageSelect = () => {},
     images = [],
     onDeleteImage = () => {},
     inputTheme = {
       value: '',
       onChange: () => {},
       errors: [],
     },
     inputMessage = {
       value: '',
       onChange: () => {},
       errors: [],
     },
     selectComplaintReason = {
       value: '',
       title: '',
       options: [{value: '', title: ''}],
       onChange: () => {},
       errors: [],
     },
     isLoading = false,
     themeColor = THEME_COLOR,
   }) => {
    let widthField = useMemo(() => WIDTH_ELEMENT - 24 * 2, []);
    return (
      <Popup
        isLoading={isLoading}
        visible={visible}
        onClose={onClose}
        onConfirm={onSendReport}
        titleConfirmBtn={'Отправить жалобу'}
        title={'Жалоба'}>
        <Input
          label={'Тема'}
          value={inputTheme.value}
          stylesContainer={{marginTop: 43}}
          width={widthField}
          theme={'inset'}
          onChange={inputTheme.onChange}
          errors={inputTheme.errors}
        />
        <Textarea
          label={'Сообщение'}
          value={inputMessage.value}
          onChange={inputMessage.onChange}
          width={widthField}
          stylesContainer={styles.fieldContainer}
          errors={inputMessage.errors}
        />
        <Select
          width={widthField}
          label={'Причина жалобы'}
          placeholder={'Причина жалобы'}
          isShadow={true}
          stylesContainer={styles.fieldContainer}
          options={selectComplaintReason.options}
          onChange={selectComplaintReason.onChange}
          value={selectComplaintReason.value}
          title={selectComplaintReason.title}
          errors={selectComplaintReason.errors}
          themeColor={themeColor}
        />
        <ScrollView
          horizontal={true}
          contentContainerStyle={[
            styles.listPhoto,
            !images.length ? {marginTop: 40} : {marginVertical: 40},
          ]}
          showsHorizontalScrollIndicator={false}>
          {images.length
            ? images.map((item) => {
              return (
                <View style={styles.imageWrapper} key={item.id}>
                  <Image
                    source={{uri: item.uri}}
                    style={styles.uploadImage}
                  />
                  <Pressable
                    style={styles.btnDelete}
                    onPress={() => onDeleteImage(item.id)}>
                    <View style={styles.line} />
                    <View style={[styles.line, styles.lineRotate]} />
                  </Pressable>
                </View>
              );
            })
            : null}
        </ScrollView>
        <Pressable style={styles.btnAttach} onPress={onShowImageSelect}>
          <Icon
            name={'attach'}
            width={'24'}
            height={'24'}
            viewBox={'0 0 24 24'}
          />
          <Text style={[styles.attachText, {color: themeColor}]}>
            Прикрепить скриншот
          </Text>
        </Pressable>
      </Popup>
    );
  },
);

const styles = StyleSheet.create({
  fieldContainer: {
    marginTop: 40,
  },
  uploadImage: {
    width: 80,
    height: 80,
    borderRadius: 4,
    resizeMode: 'cover',
  },
  btnDelete: {
    backgroundColor: BG_PRIMARY_COLOR,
    width: 16,
    height: 16,
    justifyContent: 'center',
    alignItems: 'center',
    borderRadius: 50,
    position: 'absolute',
    right: 5,
    top: 5,
    transform: [{rotateZ: '45deg'}],
  },
  line: {
    backgroundColor: ACCENT_COLOR,
    width: 7,
    height: 1,
  },
  lineRotate: {
    transform: [{rotateZ: '90deg'}, {translateX: -1}],
    height: 1,
  },
  imageWrapper: {
    position: 'relative',
    width: 80,
    height: 80,
    marginRight: 10,
  },
  images: {
    marginVertical: 28,
  },
  attachText: {
    color: TEXT_ACCENT,
    fontSize: 14,
    lineHeight: 20,
    fontFamily: FAMILY_MEDIUM,
    marginLeft: 8,
  },
  btnAttach: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 34,
  },
  listPhoto: {},
});

ReportPopup.propTypes = {
  visible: PropTypes.bool,
  onClose: PropTypes.func,
  onSendReport: PropTypes.func,
  onShowImageSelect: PropTypes.func,
  images: PropTypes.array,
  onDeleteImage: PropTypes.func,
  inputMessage: PropTypes.shape({
    value: PropTypes.string,
    onChange: PropTypes.func,
    errors: PropTypes.array,
  }),
  inputTheme: PropTypes.shape({
    value: PropTypes.string,
    onChange: PropTypes.func,
    errors: PropTypes.array,
  }),
  selectComplaintReason: PropTypes.shape({
    value: PropTypes.string,
    title: PropTypes.string,
    options: PropTypes.arrayOf(
      PropTypes.shape({value: PropTypes.string, title: PropTypes.string}),
    ),
    onChange: PropTypes.func,
    errors: PropTypes.array,
  }),
  themeColor: PropTypes.string,
};
