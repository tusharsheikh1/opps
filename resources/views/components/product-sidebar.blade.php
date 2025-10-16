<div class="admin-sidebar">
    <!-- Category Creation Form -->
    <div class="sidebar-card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-layer-group"></i>
                Book Categories
            </h4>
        </div>
        <form method="POST" id="ncategoryButton" class="card-body nc" action="{{route('admin.ncat')}}">
            @csrf
            <div id="cnm" class="alert-container"></div>
            <div class="form-group">
                <label for="ncateg" class="form-label">Category Name</label>
                <input type="text" name="ncateg" id="ncateg" class="form-control modern-input" 
                       placeholder="e.g., Fiction, Non-Fiction, Textbooks">
            </div>
            <button type="submit" class="btn btn-primary btn-modern">
                <i class="fas fa-plus-circle"></i>    
                Create Category
            </button>
        </form>
    </div>

    <!-- Sub Category Creation Form -->
    <div class="sidebar-card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-sitemap"></i>
                Sub Categories
            </h4>
        </div>
        <form method="POST" id="ncategoryButton2" class="card-body nc" action="{{route('admin.nscat')}}">
            @csrf
            <div id="cnm2" class="alert-container"></div>

            <div class="form-group">
                <label for="main" class="form-label">Parent Category</label>
                <select name="main" class="category form-control modern-select" required>
                    <option value="">Choose a main category</option>
                    @foreach ($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="ncateg2" class="form-label">Sub Category Name</label>
                <input type="text" name="ncateg" id="ncateg2" class="form-control modern-input" 
                       placeholder="e.g., Mystery, Romance, Science">
            </div>

            <button type="submit" class="btn btn-primary btn-modern">
                <i class="fas fa-plus-circle"></i>    
                Create Sub Category
            </button>
        </form>
    </div>

    <!-- Mini Category Creation Form -->
    <div class="sidebar-card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-tags"></i>
                Mini Categories
            </h4>
        </div>
        <form method="POST" id="nMiniButton" class="card-body nc" action="{{route('admin.nmcat')}}">
            @csrf
            <div id="cnm6" class="alert-container"></div>

            <div class="form-group">
                <label for="mainCategory" class="form-label">Main Category</label>
                <select name="main" id="mainCategory" class="category form-control modern-select" required>
                    <option value="">Choose a main category</option>
                    @foreach ($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nsubc" class="form-label">Sub Category</label>
                <select name="nsubc" id="nsubc" class="sub_category form-control modern-select">
                    <option value="">First select a main category</option>
                </select>
            </div>

            <div class="form-group">
                <label for="miniCat" class="form-label">Mini Category Name</label>
                <input type="text" name="miniCat" id="miniCat" class="form-control modern-input" 
                       placeholder="e.g., Bestsellers, New Arrivals">
            </div>

            <button type="submit" class="btn btn-primary btn-modern">
                <i class="fas fa-plus-circle"></i>    
                Create Mini Category
            </button>
        </form>
    </div>

    <!-- Color Management Form -->
    <div class="sidebar-card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-palette"></i>
                Book Colors
            </h4>
        </div>
        <form method="POST" id="ncolorButton" class="card-body nc" action="{{route('admin.ncolor')}}">
            @csrf
            <div id="cnm3" class="alert-container"></div>
           
            <div class="form-group">
                <label for="ncolor" class="form-label">Cover Color</label>
                <input type="text" name="ncolor" id="ncolor" class="form-control modern-input" 
                       placeholder="Select or enter color" required autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary btn-modern">
                <i class="fas fa-plus-circle"></i>    
                Add Color
            </button>
        </form>
    </div>

    <!-- Tag Management Form -->
    <div class="sidebar-card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-hashtag"></i>
                Book Tags
            </h4>
        </div>
        <form method="POST" id="nTagButton" class="card-body nc" action="{{route('admin.ntag')}}">
            @csrf
            <div id="cnm4" class="alert-container"></div>
           
            <div class="form-group">
                <label for="ntag" class="form-label">Tag Name</label>
                <input type="text" name="ntag" id="ntag" class="form-control modern-input" 
                       placeholder="e.g., Award Winner, Editor's Choice" required autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary btn-modern">
                <i class="fas fa-plus-circle"></i>    
                Create Tag
            </button>
        </form>
    </div>
</div>

<style>
.admin-sidebar {
    max-width: 350px;
    margin: 0 auto;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.sidebar-card {
    background: white;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.sidebar-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.25rem;
    border-radius: 12px 12px 0 0;
    border-bottom: none;
}

.card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-title i {
    font-size: 1.2rem;
    opacity: 0.9;
}

.card-body {
    padding: 1.5rem 1.25rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.modern-input, .modern-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.modern-input:focus, .modern-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.modern-input::placeholder {
    color: #6c757d;
    font-style: italic;
}

.btn-modern {
    width: 100%;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.btn-modern:active {
    transform: translateY(0);
}

.alert-container {
    margin-bottom: 1rem;
    min-height: auto;
}

.alert-container:empty {
    display: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-sidebar {
        max-width: 100%;
        margin: 0;
        padding: 0.75rem;
        background: transparent;
        box-shadow: none;
    }
    
    .sidebar-card {
        margin-bottom: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Modern scrollbar for select elements */
.modern-select::-webkit-scrollbar {
    width: 6px;
}

.modern-select::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modern-select::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.modern-select::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>