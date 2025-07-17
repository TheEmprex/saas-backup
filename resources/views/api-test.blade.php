<!DOCTYPE html>
<html>
<head>
    <title>Marketplace API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Marketplace API Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Login</h3>
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="admin@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" value="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
            
            <div class="col-md-6">
                <h3>API Token</h3>
                <div id="tokenDisplay" class="alert alert-info" style="display: none;"></div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h3>Test API Endpoints</h3>
                <div class="btn-group mb-3" role="group">
                    <button class="btn btn-outline-primary" onclick="testEndpoint('/api/marketplace/stats/dashboard')">Dashboard Stats</button>
                    <button class="btn btn-outline-primary" onclick="testEndpoint('/api/marketplace/job-posts')">Job Posts</button>
                    <button class="btn btn-outline-primary" onclick="testEndpoint('/api/marketplace/conversations')">Conversations</button>
                    <button class="btn btn-outline-primary" onclick="testEndpoint('/api/marketplace/profile/me')">My Profile</button>
                </div>
                
                <div id="apiResponse" class="bg-light p-3" style="min-height: 200px; font-family: monospace; white-space: pre-wrap;"></div>
            </div>
        </div>
    </div>

    <script>
        let apiToken = localStorage.getItem('api_token');
        let user = null;
        
        if (apiToken) {
            document.getElementById('tokenDisplay').style.display = 'block';
            document.getElementById('tokenDisplay').innerHTML = `Token: ${apiToken}`;
        }
        
        // Setup axios defaults
        axios.defaults.headers.common['Content-Type'] = 'application/json';
        axios.defaults.headers.common['Accept'] = 'application/json';
        
        // Login form handler
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            try {
                // Use Wave's authentication endpoint
                const response = await axios.post('/api/auth/login', {
                    email: email,
                    password: password
                });
                
                if (response.data.token) {
                    apiToken = response.data.token;
                    user = response.data.user;
                    localStorage.setItem('api_token', apiToken);
                    
                    document.getElementById('tokenDisplay').style.display = 'block';
                    document.getElementById('tokenDisplay').innerHTML = `Token: ${apiToken}`;
                    
                    // Set authorization header
                    axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken}`;
                    
                    document.getElementById('apiResponse').textContent = 'Login successful!\\n' + JSON.stringify(response.data, null, 2);
                } else {
                    document.getElementById('apiResponse').textContent = 'Login failed: ' + JSON.stringify(response.data, null, 2);
                }
            } catch (error) {
                document.getElementById('apiResponse').textContent = 'Login error: ' + error.message;
                console.error('Login error:', error);
            }
        });
        
        // Test endpoint function
        async function testEndpoint(endpoint) {
            if (!apiToken) {
                document.getElementById('apiResponse').textContent = 'Please login first';
                return;
            }
            
            try {
                const response = await axios.get(endpoint, {
                    headers: {
                        'Authorization': `Bearer ${apiToken}`
                    }
                });
                
                document.getElementById('apiResponse').textContent = `GET ${endpoint}\\n\\nResponse:\\n` + JSON.stringify(response.data, null, 2);
            } catch (error) {
                document.getElementById('apiResponse').textContent = `GET ${endpoint}\\n\\nError:\\n` + JSON.stringify(error.response?.data || error.message, null, 2);
                console.error('API error:', error);
            }
        }
        
        // Set token if already exists
        if (apiToken) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken}`;
        }
    </script>
</body>
</html>
