import React, {useState} from 'react';
import MapView,{PROVIDER_GOOGLE} from 'react-native-maps';   //maps
export default function App() {

  const initialRegion =  {
    latitude: 45.4735448,
    longitude: -73.5639533,
    latitudeDelta: 1,  
    longitudeDelta: 1
  }
  return (
      <MapView style={{flex:1}} 
              showsUserLocation 
              provider={PROVIDER_GOOGLE}
              initialRegion={initialRegion}/>
  );
}

