<!DOCTYPE html>
<html lang="te">
 <head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>రోడ్ హెల్ప్ నెట్‌వర్క్ (Roadside Assistance)</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: auto; }
        h1, h2 { text-align: center; color: #333; }
        
        /* కార్డ్ డిజైన్ */
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"], textarea, input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        
        /* బటన్స్ */
        button { background-color: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 16px; width: 100%; }
        button:hover { background-color: #218838; }
        .loc-btn { background-color: #007bff; margin-top: 5px; }
        .loc-btn:hover { background-color: #0069d9; }
        
        /* Before and After ఫోటోలు పక్కపక్కన చూపించడానికి */
        .photo-comparison { display: flex; gap: 10px; margin-top: 15px; }
        .photo-box { flex: 1; text-align: center; border: 1px solid #ddd; padding: 5px; border-radius: 5px; background: #fafafa; }
        .photo-box img { width: 100%; height: 180px; object-fit: cover; border-radius: 5px; }
        .photo-box span { font-weight: bold; display: block; margin-bottom: 5px; }
        
        /* కామెంట్స్ సెక్షన్ */
        .comments-section { margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px; }
        .comment-list { list-style: none; padding: 0; margin-top: 10px; font-size: 14px; color: #555; }
        .comment-list li { background: #eef2f5; padding: 6px 10px; border-radius: 4px; margin-bottom: 5px; }
        
        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; color: white; font-weight: bold; }
        .pending { background-color: #ffc107; color: #333; }
        .resolved { background-color: #28a745; }
    </style>
</head>
<body>

<div class="container">
    <h1>సహాయం అడగండి / చేయండి</h1>

    <!-- 1. పోస్ట్ లేదా రిక్వెస్ట్ ఫారమ్ -->
    <div class="card">
        <h2>సమస్యను పోస్ట్ చేయండి</h2>
        <div class="form-group">
            <label>మీ సమస్య వివరాలు:</label>
            <textarea id="problemText" rows="3" placeholder="ఉదా: నా బైక్ పెట్రోల్ అయిపోయింది / టైర్ పంచర్ అయింది..."></textarea>
        </div>
        
        <div class="form-group">
            <label>మీ లొకేషన్:</label>
            <button class="loc-btn" onclick="getLocation()">📍 నా ప్రస్తుత లొకేషన్ తీసుకో</button>
            <input type="text" id="locationUrl" placeholder="లొకేషన్ లింక్ ఇక్కడ కనిపిస్తుంది" readonly style="margin-top:5px;">
        </div>

        <div class="form-group">
            <label>సమస్య ఉన్న ఫోటో (Before Photo):</label>
            <input type="file" id="beforeImageInput" accept="image/*">
        </div>

        <button onclick="createPost()">రిక్వెస్ట్ సమర్పించు (Submit)</button>
    </div>

    <!-- 2. పోస్ట్‌లు కనిపించే ప్రదేశం -->
    <h2>ఇటీవలి రిక్వెస్ట్‌లు</h2>
    <div id="postsContainer"></div>
</div>

<script>
    let tempLocation = "";

    // 1. లొకేషన్ (GPS) పొందే ఫంక్షన్
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                tempLocation = `https://www.google.com/maps?q=${lat},${lon}`;
                document.getElementById('locationUrl').value = tempLocation;
                alert("లొకేషన్ విజయవంతంగా స్వీకరించబడింది!");
            }, function() {
                alert("లొకేషన్ తీసుకోవడంలో సమస్య వచ్చింది. ದయచేసి అనుమతి (Permission) ఇవ్వండి.");
            });
        } else {
            alert("మీ బ్రౌజర్ లొకేషన్‌కు సపోర్ట్ చేయడం లేదు.");
        }
    }

    // 2. కొత్త పోస్ట్ క్రియేట్ చేయడం
    function createPost() {
        const text = document.getElementById('problemText').value;
        const location = document.getElementById('locationUrl').value;
        const imageFile = document.getElementById('beforeImageInput').files[0];

        if (!text || !imageFile) {
            alert("దయచేసి సమస్య వివరాలు మరియు ఫోటోను అప్‌లోడ్ చేయండి!");
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const beforeImgSrc = e.target.result;
            const postId = 'post-' + Date.now();

            const postHTML = `
                <div class="card" id="${postId}">
                    <span class="status-badge pending" id="status-${postId}">సహాయం కావాలి (Pending)</span>
                    <p style="margin-top:10px; font-size:18px;"><strong>సమస్య:</strong> ${text}</p>
                    ${location ? `<p>📍 <a href="${location}" target="_blank">Google Maps లో లొకేషన్ చూడండి</a></p>` : ''}

                    <!-- ఫోటోల కాంపాటిబిలిటీ (Before & After) -->
                    <div class="photo-comparison">
                        <div class="photo-box">
                            <span style="color:red;">Before (సమస్య ఉన్నప్పుడు)</span>
                            <img src="${beforeImgSrc}" alt="Before Photo">
                        </div>
                        <div class="photo-box" id="afterBox-${postId}" style="display:none;">
                            <span style="color:green;">After (సహాయం చేసిన తర్వాత)</span>
                            <img id="afterImg-${postId}" src="" alt="After Photo">
                        </div>
                    </div>

                    <!-- సహాయం చేసిన తర్వాత ఆఫ్టర్ ఫోటో అప్‌లోడ్ సెక్షన్ -->
                    <div id="helpUploadSection-${postId}" style="margin-top:15px; background:#f9f9f9; padding:10px; border-radius:5px;">
                        <label style="font-size:14px;">సహాయం చేశారా? "After Photo" అప్‌లోడ్ చేయండి:</label>
                        <input type="file" id="afterInput-${postId}" accept="image/*" style="margin-bottom:5px;">
                        <button onclick="resolveHelp('${postId}')" style="background:#007bff; font-size:14px; padding:5px 10px;">హెల్ప్ పూర్తయింది అని మార్చు</button>
                    </div>

                    <!-- కామెంట్స్ సెక్షన్ -->
                    <div class="comments-section">
                        <h4>కామెంట్స్:</h4>
                        <ul class="comment-list" id="comments-${postId}"></ul>
                        <input type="text" id="commentInput-${postId}" placeholder="మీ కామెంట్ రాయండి..." style="width:75%; display:inline;">
                        <button onclick="addComment('${postId}')" style="width:20%; display:inline; padding:10px 5px; font-size:14px;">Add</button>
                    </div>
                </div>
            `;

            document.getElementById('postsContainer').insertAdjacentHTML('afterbegin', postHTML);

            // ఫారమ్ రీసెట్ చేయడం
            document.getElementById('problemText').value = '';
            document.getElementById('locationUrl').value = '';
            document.getElementById('beforeImageInput').value = '';
        };

        reader.readAsDataURL(imageFile);
    }

    // 3. ఆఫ్టర్ ఫోటో అప్‌లోడ్ చేసి స్టేటస్ అప్‌డేట్ చేయడం
    function resolveHelp(postId) {
        const fileInput = document.getElementById(`afterInput-${postId}`);
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(`afterImg-${postId}`).src = e.target.result;
                document.getElementById(`afterBox-${postId}`).style.display = 'block';
                
                // స్టేటస్ బ్యాడ్జ్ మార్చడం
                const badge = document.getElementById(`status-${postId}`);
                badge.innerText = "సహాయం అందించబడింది (Resolved)";
                badge.className = "status-badge resolved";

                // అప్‌లోడ్ సెక్షన్‌ను దాచడం
                document.getElementById(`helpUploadSection-${postId}`).style.display = 'none';
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            alert("దయచేసి సహాయం అందించిన తర్వాత తీసిన (After) ఫోటోను ఎంచుకోండి!");
        }
    }

    // 4. కామెంట్ యాడ్ చేయడం
    function addComment(postId) {
        const commentInput = document.getElementById(`commentInput-${postId}`);
        const commentText = commentInput.value.trim();

        if (commentText !== "") {
            const commentList = document.getElementById(`comments-${postId}`);
            const li = document.createElement('li');
            li.textContent = commentText;
            commentList.appendChild(li);
            commentInput.value = "";
        }
    }
</script>

</body>
 </html>



