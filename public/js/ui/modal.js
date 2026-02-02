window.confirmAction = function() {
    window.closeCustomModal(); // Tutup dulu
    if (pendingActionType === "SAMBUNG") {
        executeRedirect(pendingBudget, pendingParentId, pendingChildId);
    } else if (pendingActionType === "REROUTE") {
        executeRerouteLogic(pendingParentId, pendingChildId, pendingBudget);
    } else if (pendingActionType === "HAPUS") {
        var url = new URL(window.location.href);
        var currentIds = url.searchParams.getAll('rute_fix[]');
        if(currentIds.length === 0) currentIds = url.searchParams.getAll('rute_fix');
        
        var newIds = currentIds.filter(id => id.toString() !== pendingChildId.toString());
        url.searchParams.delete('rute_fix[]');
        url.searchParams.delete('rute_fix');
        url.searchParams.delete('parent_id'); 
        
        newIds.forEach(id => { url.searchParams.append('rute_fix[]', id); });
        window.location.href = url.toString();
    }
};

window.closeCustomModal = function() {
    var modal = document.getElementById('custom-modal-overlay');
    if(modal) modal.style.display = 'none';
};

function showCustomModal(icon, title, desc, btnText, btnColor) {
    var htmlContent = `
        <div style="text-align:center; margin-bottom:15px;">
            <div style="font-size:3rem;">${icon}</div><h3>${title}</h3>
        </div>
        <p>${desc}</p>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button onclick="window.closeCustomModal()" class="cm-btn cm-btn-cancel">Batal</button>
            <button onclick="window.confirmAction()" class="cm-btn" style="background: ${btnColor}; color: white;">${btnText}</button>
        </div>`;
    var container = document.querySelector('#custom-modal-overlay > div');
    if(container) {
        container.innerHTML = htmlContent;
        document.getElementById('custom-modal-overlay').style.display = 'flex';
    } else {
        console.error("Modal Container Missing in HTML");
    }
}