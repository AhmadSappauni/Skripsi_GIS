<div id="visitedModal" class="modal-overlay" style="display: none; z-index: 10000; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px);">
    
    <div class="modal-content" style="background: #fff; width: 95%; max-width: 480px; height: 85vh; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);">
        
        <div style="background: white; padding: 20px 25px; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; z-index: 10;">
            <div>
                <h3 style="margin: 0; font-size: 20px; color: #0f172a; font-weight: 800; letter-spacing: -0.5px;">Jurnal Perjalanan</h3>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Koleksi momen terbaikmu ✨</p>
            </div>
            <button onclick="closeVisitedModal()" style="width: 36px; height: 36px; border-radius: 50%; border: 1px solid #e2e8f0; background: white; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;">✕</button>
        </div>

        <div class="directory-body" id="visitedListContainer" style="padding: 0; flex-grow: 1; overflow-y: auto; background: #f8fafc;">
            </div>

        <div style="padding: 15px; background: white; border-top: 1px solid #f1f5f9; text-align: center; flex-shrink: 0;">
             <span style="font-size: 11px; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; font-weight: 700;">Smart Itinerary Journal</span>
        </div>
    </div>
</div>