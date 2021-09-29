import React from 'react';
import {StyleSheet, View, Text, Image, Pressable} from 'react-native';
import {observer} from 'mobx-react';
import PropTypes from 'prop-types';
import {Popup} from '../../../components/Popups';
import {Input} from '../../../components/Input';
import {FAMILY_REGULAR, WIDTH_ELEMENT} from '../../../constants';
import {ButtonControl} from '../../../components/Button/ButtonControl';
import IconXML from '../../../components/Icon/IconXML';

const WIDTH_INPUT = WIDTH_ELEMENT - 24 * 2;

function Option({
                  label = '',
                  value = '',
                  onChange = () => {},
                  onDelete = () => {},
                  showDeleteButton = false,
                  errors = [],
                }) {
  return (
    <View style={styles.optionWrapper}>
      <Input
        label={label}
        value={value}
        onChange={onChange}
        theme={'inset'}
        width={WIDTH_INPUT}
        errors={errors}
      />
      {showDeleteButton ? (
        <Pressable hitSlop={5} onPress={onDelete} style={styles.deleteBtn}>
          <IconXML name={'close'} width={15} height={15} />
        </Pressable>
      ) : null}
    </View>
  );
}

export let CreateSurveyPopup = observer(
  ({
     visible = false,
     onClose = () => {},
     onCreateSurvey = () => {},
     listOptions = [],
     addOption = () => {},
     changeOption = () => {},
     deleteOption = () => {},
     isAcceptableCountOption = true,
     inputTheme = {
       value: '',
       onChange: () => {},
       errors: [],
     },
     isLoading = false,
   }) => {
    return (
      <Popup
        visible={visible}
        onClose={onClose}
        title={'Опрос'}
        titleConfirmBtn={'Создать опрос'}
        onConfirm={onCreateSurvey}
        isLoading={isLoading}>
        <Input
          label={'Тема'}
          value={inputTheme.value}
          errors={inputTheme.errors}
          onChange={inputTheme.onChange}
          theme={'inset'}
          width={WIDTH_INPUT}
          stylesContainer={{marginTop: 43}}
        />
        {listOptions.length
          ? listOptions.map((option) => (
            <Option
              key={option.id}
              value={option.value}
              label={option.label}
              onChange={changeOption(option)}
              showDeleteButton={option.showDeleteButton}
              onDelete={() => deleteOption(option.id)}
              errors={option.errors}
            />
          ))
          : null}

        <View style={styles.addWrapper}>
          {isAcceptableCountOption ? (
            <>
              <Text style={styles.addText}>Добавить вариант</Text>
              <ButtonControl
                onPress={addOption}
                width={32}
                height={32}
                borderRadius={30}
                radiusShadow={14}
                icon={
                  <Image
                    source={require('../../../assets/images/icons/add.png')}
                    style={styles.iconAdd}
                  />
                }
              />
            </>
          ) : null}
        </View>
      </Popup>
    );
  },
);

CreateSurveyPopup.propTypes = {
  visible: PropTypes.bool,
  isAcceptableCountOption: PropTypes.bool,
  onClose: PropTypes.func,
  onCreateSurvey: PropTypes.func,
  addOption: PropTypes.func,
  changeOption: PropTypes.func,
  deleteOption: PropTypes.func,
  listOptions: PropTypes.array,
  inputTheme: PropTypes.shape({
    value: PropTypes.string,
    onChange: PropTypes.func,
    errors: PropTypes.array,
  }),
  isLoading: PropTypes.bool,
};

const styles = StyleSheet.create({
  iconAdd: {
    width: 12,
    height: 12,
    resizeMode: 'cover',
  },
  addWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 28,
    marginBottom: 44,
  },
  addText: {
    color: '#666',
    fontSize: 14,
    lineHeight: 25,
    fontFamily: FAMILY_REGULAR,
  },
  optionWrapper: {
    position: 'relative',
    marginTop: 40,
  },
  deleteBtn: {
    position: 'absolute',
    width: 15,
    height: 15,
    right: 10,
    top: 13,
  },
});
