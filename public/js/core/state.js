var map = null;
var userMarker = null;
var userCircle = null;
var allMarkers = {};       
var alternativeLayer = null; 
var radiusCircle = null;    
var pendingParentId = null;
var pendingChildId = null;
var pendingBudget = null;
var pendingActionType = "";
var activeMarkerId = null;
var originalIcons = {};
var focusLayer = null;
var geoJsonLayer = null;

var redIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
var greyIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [20, 32], iconAnchor: [10, 32], popupAnchor: [1, -28], shadowSize: [32, 32] });
var highlightIcon = new L.Icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [35, 55], iconAnchor: [17, 55], popupAnchor: [1, -34], shadowSize: [41, 41] });

var LeafIcon = L.Icon.extend({ options: { iconSize: [30, 50], iconAnchor: [13, 50], popupAnchor: [0, -10] } });
var icons = {
    Alam:    new LeafIcon({ iconUrl: '/icons/alam.png' }),
    Religi:  new LeafIcon({ iconUrl: '/icons/religi.png' }),
    Kuliner: new LeafIcon({ iconUrl: '/icons/kuliner.png' }),
    Belanja: new LeafIcon({ iconUrl: '/icons/belanja.png' }),
    Budaya:  new LeafIcon({ iconUrl: '/icons/budaya.png'}),
    Edukasi: new LeafIcon({ iconUrl: '/icons/edukasi.png'}),
    Agro:    new LeafIcon({ iconUrl: '/icons/agro.png'}),
    Rekreasi: new LeafIcon({ iconUrl: '/icons/rekreasi.png'}),
    Default: new LeafIcon({ iconUrl: '/icons/default.png' }),
};