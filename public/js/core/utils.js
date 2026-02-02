function getImageUrl(dbImage) {
    // fallback
    if (!dbImage) {
        return 'https://placehold.co/400x300/e2e8f0/64748b?text=No+Image';
    }

    // base64 atau URL eksternal
    if (dbImage.startsWith('data:') || dbImage.startsWith('http')) {
        return dbImage;
    }

    // pastikan path absolut
    if (dbImage.startsWith('/')) {
        return dbImage;
    }

    // path dari database (uploads/wisata/xxx.png)
    return '/' + dbImage;
}


function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
    var R = 6371; 
    var dLat = deg2rad(lat2-lat1);  
    var dLon = deg2rad(lon2-lon1); 
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
    return R * c;
}

function deg2rad(deg) { return deg * (Math.PI/180); }