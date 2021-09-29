import React from 'react';
import {Image, StyleSheet, Text, View, ActivityIndicator} from 'react-native';
import PropTypes from 'prop-types';
import {HeaderButtonMore} from '../../navigation/components/HeaderButtonMore';
import {
  FAMILY_MEDIUM,
  FAMILY_REGULAR,
  THEME_COLOR,
  WINDOW_WIDTH,
} from '../../constants';
import {HeaderButtonBack} from '../../navigation/components/HeaderButtonBack';

function HeaderTitle({title = '', countParticipants = 0}) {
  return (
    <View style={styles.headerTextWrapper}>
      <Text style={styles.title} numberOfLines={1}>
        {title}
      </Text>
      <Text style={styles.countParticipants}>
        {countParticipants} участников
      </Text>
    </View>
  );
}

export function settingsNavigation(
  navigation,
  onMore = () => {},
  dataForNavigation = {
    image: '',
    countMember: 0,
    theme: '',
  },
  isLoading = false,
  themeColor = THEME_COLOR,
) {
  navigation.setOptions({
    headerBackImage: (props) => (
      <HeaderButtonBack
        backgroundColor={
          dataForNavigation.image ? 'rgba(255, 255, 255, 0.16)' : themeColor
        }
        opacityBottomShadow={dataForNavigation.image ? 0.16 : 0.08}
        {...props}
      />
    ),
    headerBackground: () => (
      <View style={[StyleSheet.absoluteFill]}>
        {dataForNavigation.image ? (
          <>
            <Image
              source={{uri: dataForNavigation.image}}
              style={[styles.headerImage]}
            />
            <View style={styles.overlayHeader} />
          </>
        ) : (
          <View
            style={[styles.headerBackground, {backgroundColor: themeColor}]}
          />
        )}
      </View>
    ),
    headerTitle: (props) => {
      return isLoading ? (
        <ActivityIndicator
          style={styles.headerTextWrapper}
          size={'small'}
          color={'white'}
        />
      ) : (
        <HeaderTitle
          title={dataForNavigation.theme}
          countParticipants={dataForNavigation.countMember}
          {...props}
        />
      );
    },
    headerRight: (props) => (
      <HeaderButtonMore
        backgroundColor={
          dataForNavigation.image ? 'rgba(255, 255, 255, 0.16)' : themeColor
        }
        opacityBottomShadow={dataForNavigation.image ? 0.16 : 0.08}
        onPress={onMore}
        {...props}
      />
    ),
  });
}

settingsNavigation.propTypes = {
  navigation: PropTypes.object,
  onMore: PropTypes.func,
  dataForNavigation: PropTypes.shape({
    image: PropTypes.string,
    countMember: PropTypes.number,
    title: PropTypes.string,
  }),
};

const styles = StyleSheet.create({
  headerImage: {
    width: WINDOW_WIDTH,
    height: 104,
    resizeMode: 'cover',
    borderBottomRightRadius: 16,
    borderBottomLeftRadius: 16,
  },
  overlayHeader: {
    backgroundColor: 'rgba(0, 0, 0, 0.64)',
    width: '100%',
    height: 104,
    position: 'absolute',
    left: 0,
    top: 0,
    borderBottomRightRadius: 16,
    borderBottomLeftRadius: 16,
  },
  title: {
    fontSize: 16,
    lineHeight: 24,
    color: 'white',
    fontFamily: FAMILY_MEDIUM,
    marginBottom: 4,
    textAlign: 'center',
  },
  countParticipants: {
    fontSize: 12,
    lineHeight: 16,
    color: 'rgba(255, 255, 255, 0.56)',
    fontFamily: FAMILY_REGULAR,
    textAlign: 'center',
  },
  headerTextWrapper: {
    paddingHorizontal: 45,
  },
  headerBackground: {
    width: WINDOW_WIDTH,
    height: 104,
    borderBottomRightRadius: 16,
    borderBottomLeftRadius: 16,
  },
});
