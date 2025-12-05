import React, { useState, useEffect, useCallback, useMemo } from 'react';
import axios from 'axios';
import { LogIn, User, BookOpen, Settings, Home, Plus, Trash2, RotateCcw, Save } from 'lucide-react';

// Configure Axios to point to the Flask backend
const API_BASE_URL = 'http://localhost:5001/api';
axios.defaults.baseURL = API_BASE_URL;
axios.defaults.headers.common['Content-Type'] = 'application/json';

// --- Global Setup for Tailwind CDN ---
// We inject Tailwind via CDN immediately to bypass local build system errors (like PostCSS errors)
// This must be outside of the component to ensure it runs immediately upon script loading.
(function() {
  if (!document.getElementById('tailwind-cdn')) {
    const script = document.createElement('script');
    script.src = 'https://cdn.tailwindcss.com';
    script.id = 'tailwind-cdn';
    document.head.appendChild(script);
  }
})();


// --- Utility Functions and Hooks ---

// Hook to manage global application state
const useAppState = () => {
  const [user, setUser] = useState(() => {
    const savedUser = localStorage.getItem('user_data');
    return savedUser ? JSON.parse(savedUser) : null;
  });
  const [token, setToken] = useState(localStorage.getItem('jwt_token'));
  const [route, setRoute] = useState('home');
  const [message, setMessage] = useState({ text: '', type: '' });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (user) {
      localStorage.setItem('user_data', JSON.stringify(user));
    }
  }, [user]);

  const login = useCallback((userData, jwtToken) => {
    console.log('Login called with:', { userData, jwtToken });
    
    // Store token
    localStorage.setItem('jwt_token', jwtToken);
    setToken(jwtToken);
    
    // Store user data
    localStorage.setItem('user_data', JSON.stringify(userData));
    setUser(userData);
    
    // Update axios headers
    axios.defaults.headers.common['Authorization'] = `Bearer ${jwtToken}`;
    
    // Change route
    setRoute('dashboard');
  }, []);

  const logout = useCallback(() => {
    // Clear all auth data
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('user_data');
    delete axios.defaults.headers.common['Authorization'];
    setToken(null);
    setUser(null);
    setRoute('home');
  }, []);

  return {
    user,
    token,
    route,
    setRoute,
    login,
    logout,
    message,
    loading,
    setLoading,
    notify: (text, type = 'success') => {
      setMessage({ text, type });
      setTimeout(() => setMessage({ text: '', type: '' }), 4000);
    }
  };
};

// --- General Components ---

const Notification = ({ message }) => {
  if (!message.text) return null;
  const classes = message.type === 'success' 
    ? 'bg-green-500 border-green-700' 
    : 'bg-red-500 border-red-700';

  return (
    <div className={`fixed top-4 right-4 z-50 p-4 rounded-lg text-white font-semibold shadow-xl transition-all duration-300 ${classes}`}>
      {message.text}
    </div>
  );
};

const Header = ({ user, setRoute, logout }) => {
  // Add console log to debug user state
  console.log('Header user state:', user);

  return (
    <header className="bg-gray-900 shadow-xl text-white sticky top-0 z-40">
      <div className="container mx-auto px-4 py-4 flex justify-between items-center">
        <div className="flex items-center space-x-4 cursor-pointer" onClick={() => setRoute('home')}>
          <BookOpen className="h-7 w-7 text-indigo-400" />
          <h1 className="text-2xl font-bold text-indigo-100">LMS Pro</h1>
        </div>
        
        <nav className="flex items-center space-x-4">
          <NavItem icon={Home} label="Home" onClick={() => setRoute('home')} />
          
          {user ? (
            // Logged in navigation items
            <>
              <NavItem icon={BookOpen} label="Books" onClick={() => setRoute('dashboard')} />
              <NavItem icon={User} label="Profile" onClick={() => setRoute('profile')} />
              <button
                onClick={logout}
                className="flex items-center space-x-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
              >
                <LogIn className="h-5 w-5 rotate-180" />
                <span>Logout ({user.username})</span>
              </button>
            </>
          ) : (
            // Not logged in navigation items
            <>
              <NavItem icon={LogIn} label="Login" onClick={() => setRoute('login')} />
              <NavItem icon={User} label="Register" onClick={() => setRoute('register')} />
            </>
          )}
        </nav>
      </div>
    </header>
  );
};

// Update the NavItem component as well
const NavItem = ({ icon: Icon, label, onClick }) => (
  <button
    onClick={onClick}
    className="flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"
  >
    <Icon className="h-5 w-5" />
    <span>{label}</span>
  </button>
);

const Card = ({ title, children, className = '' }) => (
  <div className={`bg-white p-6 rounded-xl shadow-2xl transition-all duration-300 ${className}`}>
    {title && <h2 className="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">{title}</h2>}
    {children}
  </div>
);

const InputField = ({ label, id, type = 'text', value, onChange, placeholder, required = false, name }) => (
  <div className="mb-4">
    <label htmlFor={id} className="block text-sm font-medium text-gray-700 mb-1">
      {label}
    </label>
    <input
      type={type}
      id={id}
      name={name || id}
      value={value}
      onChange={onChange}
      placeholder={placeholder}
      required={required}
      className="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition duration-150"
    />
  </div>
);

// --- Auth Components ---

const AuthForm = ({ title, route, state, setState, appState }) => {
  const isLogin = route === 'login';
  const { login, notify, setLoading } = appState;
  const [error, setError] = useState('');

  const onSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      const response = await axios.post(isLogin ? '/login' : '/register', 
        isLogin ? {
          email: state.identifier,
          password: state.password
        } : {
          username: state.username,
          email: state.email,
          password: state.password,
          role: 'user'
        }
      );

      console.log('Server response:', response.data);

      if (isLogin) {
        if (response.data.token && response.data.user) {
          await login(response.data.user, response.data.token);
          notify('Login successful!', 'success');
        } else {
          throw new Error('Invalid server response');
        }
      } else {
        notify('Registration successful! Please log in.', 'success');
        appState.setRoute('login');
      }
    } catch (error) {
      console.error('Auth error:', error);
      const message = error.response?.data?.msg || error.message;
      setError(message);
      notify(message, 'error');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Card title={title} className="max-w-md mx-auto my-12">
      <form onSubmit={onSubmit}>
        {error && (
          <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {error}
          </div>
        )}
        {!isLogin && (
          <InputField 
            label="Username" 
            id="username" 
            value={state.username} 
            onChange={(e) => setState({ ...state, username: e.target.value })} 
            required 
          />
        )}
        <InputField 
          label={isLogin ? "Email" : "Email"}  // Changed label from "Username or Email"
          id={isLogin ? "identifier" : "email"} 
          type="email"  // Set type to email for validation
          value={isLogin ? state.identifier : state.email} 
          onChange={(e) => setState({ ...state, [isLogin ? 'identifier' : 'email']: e.target.value })} 
          required 
        />
        <InputField 
          label="Password" 
          id="password" 
          type="password" 
          value={state.password} 
          onChange={(e) => setState({ ...state, password: e.target.value })} 
          required 
        />
        <button
          type="submit"
          disabled={appState.loading}
          className="w-full bg-indigo-600 text-white p-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-200 shadow-md disabled:bg-indigo-400"
        >
          {appState.loading ? 'Processing...' : title}
        </button>
      </form>
      <p className="mt-4 text-center text-sm text-gray-500">
        {isLogin ? "Don't have an account? " : "Already have an account? "}
        <button onClick={() => setRoute(isLogin ? 'register' : 'login')} className="text-indigo-600 hover:text-indigo-800 font-medium">
          {isLogin ? 'Register here' : 'Login here'}
        </button>
      </p>
    </Card>
  );
};

const RegisterPage = ({ appState }) => {
  const [form, setForm] = useState({ username: '', email: '', password: '' });
  return <AuthForm title="Create Account" route="register" state={form} setState={setForm} appState={appState} />;
};

const LoginPage = ({ appState }) => {
  const [form, setForm] = useState({ identifier: '', password: '' });
  return <AuthForm title="Sign In" route="login" state={form} setState={setForm} appState={appState} />;
};

// --- Profile Component ---

const ProfilePage = ({ appState }) => {
  const { user, notify, fetchProfile, isAdmin, isLibrarianOrAdmin } = appState;
  const [file, setFile] = useState(null);
  const [isUploading, setIsUploading] = useState(false);

  // Auto-fetch profile data when entering the page
  useEffect(() => {
    fetchProfile();
  }, [fetchProfile]);

  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
  };

  const handleUpload = async () => {
    if (!file) {
      notify('Please select an image to upload.', 'error');
      return;
    }

    setIsUploading(true);
    const formData = new FormData();
    formData.append('avatar', file);

    try {
      const res = await axios.post('/profile/avatar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      notify('Avatar updated successfully!', 'success');
      setFile(null); // Clear the file input
      fetchProfile(); // Refresh profile data
    } catch (error) {
      const message = error.response?.data?.msg || 'Failed to upload avatar.';
      notify(message, 'error');
    } finally {
      setIsUploading(false);
    }
  };
  
  const handleReturn = async (borrowingId, title) => {
      // NOTE: Using window.confirm() as a substitute for a custom modal in this environment.
      if (!window.confirm(`Are you sure you want to return the book: "${title}"?`)) return;

      try {
          await axios.post(`/return/${borrowingId}`);
          notify(`'${title}' returned successfully!`, 'success');
          fetchProfile(); // Refresh history
      } catch (error) {
          notify(error.response?.data?.msg || 'Failed to return book.', 'error');
      }
  };

  if (!user) return <p className="text-center text-xl mt-12 text-gray-600">Please log in to view your profile.</p>;

  return (
    <div className="container mx-auto px-4 py-8 grid lg:grid-cols-3 gap-8">
      {/* Profile Card */}
      <Card className="lg:col-span-1 h-fit">
        <div className="flex flex-col items-center">
          <div className="relative w-32 h-32 rounded-full overflow-hidden mb-4 border-4 border-indigo-500 shadow-lg">
            <img 
              src={user.avatar_url || 'https://placehold.co/128x128/5E5470/FFFFFF?text=P'} 
              alt="User Avatar" 
              className="object-cover w-full h-full"
              onError={(e) => { e.target.onerror = null; e.target.src="https://placehold.co/128x128/5E5470/FFFFFF?text=P" }}
            />
          </div>
          <h3 className="text-3xl font-bold text-gray-900">{user.username}</h3>
          <p className={`text-sm font-semibold mt-1 py-1 px-3 rounded-full ${isAdmin ? 'bg-red-100 text-red-600' : isLibrarianOrAdmin ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600'}`}>
            {user.role.toUpperCase()}
          </p>
          <div className="mt-6 w-full space-y-2 text-gray-600">
            <p className="flex justify-between items-center border-b pb-1">Email: <span className="font-medium text-gray-800">{user.email}</span></p>
            <p className="flex justify-between items-center">Member Since: <span className="font-medium text-gray-800">{new Date(user.created_at).toLocaleDateString()}</span></p>
            <p className="text-xs break-words pt-4 text-center text-gray-400">User ID: {user.id}</p>
          </div>
        </div>
      </Card>
      
      {/* Avatar Upload and History */}
      <div className="lg:col-span-2 space-y-8">
        <Card title="Change Avatar">
          <div className="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 items-center">
            <input type="file" onChange={handleFileChange} accept="image/*" className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            <button
              onClick={handleUpload}
              disabled={!file || isUploading}
              className="w-full sm:w-auto flex-shrink-0 bg-green-600 text-white p-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200 shadow-md disabled:bg-green-400"
            >
              {isUploading ? 'Uploading...' : 'Save Avatar'}
            </button>
          </div>
          <p className="mt-2 text-sm text-gray-500">Max file size: 2MB. Allowed types: PNG, JPG, JPEG, GIF.</p>
        </Card>

        <Card title="Borrowing History">
          {(user.borrowing_history || []).length === 0 ? (
            <p className="text-gray-500">You haven't borrowed any books yet.</p>
          ) : (
            <div className="space-y-4 max-h-96 overflow-y-auto pr-2">
              {user.borrowing_history.map((item) => (
                <div key={item.id} className="p-4 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition duration-200 flex justify-between items-center bg-gray-50">
                  <div>
                    <p className="font-semibold text-gray-900">{item.book_title}</p>
                    <p className="text-sm text-gray-600">Borrowed: {new Date(item.borrow_date).toLocaleDateString()}</p>
                    <p className="text-sm text-red-500 font-medium">Due: {new Date(item.due_date).toLocaleDateString()}</p>
                  </div>
                  <div className="flex items-center space-x-3">
                    <span className={`px-3 py-1 text-xs font-semibold rounded-full ${
                      item.status === 'returned' ? 'bg-green-100 text-green-600' : 
                      (new Date(item.due_date) < new Date() ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600')
                    }`}>
                      {item.status.toUpperCase()}
                    </span>
                    {item.status === 'borrowed' && (
                        <button 
                            onClick={() => handleReturn(item.id, item.book_title)}
                            className="p-2 bg-indigo-500 text-white rounded-full hover:bg-indigo-600 transition shadow-md"
                            title="Return Book"
                        >
                            <RotateCcw className="w-5 h-5" />
                        </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </Card>
      </div>
    </div>
  );
};

// --- Book Management Component (Admin/Librarian) ---

const BookManager = ({ appState, books, fetchBooks }) => {
  const { isLibrarianOrAdmin, isAdmin, notify, setLoading } = appState;
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [currentBook, setCurrentBook] = useState(null);
  const [form, setForm] = useState({});

  useEffect(() => {
    if (currentBook) {
      setForm(currentBook);
    } else {
      setForm({ title: '', author: '', isbn: '', published_year: '', stock: 1, genre: '' });
    }
  }, [currentBook]);

  if (!isLibrarianOrAdmin) {
    return <p className="text-center text-red-500 text-xl mt-12">Authorization Required: Only Librarians and Admins can access Book Management.</p>;
  }

  const handleOpenModal = (book = null) => {
    setCurrentBook(book);
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
    setCurrentBook(null);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setForm(prev => ({ ...prev, [name]: name === 'stock' || name === 'published_year' ? parseInt(value) || '' : value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      if (currentBook) {
        // Update
        await axios.put(`/books/${currentBook.id}`, form);
        notify('Book updated successfully!', 'success');
      } else {
        // Create
        await axios.post('/books', form);
        notify('Book added successfully!', 'success');
      }
      fetchBooks();
      handleCloseModal();
    } catch (error) {
      notify(error.response?.data?.msg || 'Error saving book data.', 'error');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (bookId, title) => {
    if (!isAdmin) {
      notify('Only Admins can delete books.', 'error');
      return;
    }
    // NOTE: Using window.confirm() as a substitute for a custom modal in this environment.
    if (!window.confirm(`Are you sure you want to delete the book: "${title}"?`)) return;

    try {
      setLoading(true);
      await axios.delete(`/books/${bookId}`);
      notify('Book deleted successfully!', 'success');
      fetchBooks();
    } catch (error) {
      notify(error.response?.data?.msg || 'Error deleting book.', 'error');
    } finally {
      setLoading(false);
    }
  };
  
  // Modal for Add/Edit Book
  const BookModal = () => (
    <div className="fixed inset-0 bg-gray-900 bg-opacity-75 flex justify-center items-center z-50 p-4">
      <Card title={currentBook ? "Edit Book" : "Add New Book"} className="w-full max-w-lg">
        <form onSubmit={handleSubmit}>
          <InputField label="Title" id="title" name="title" value={form.title || ''} onChange={handleInputChange} required />
          <InputField label="Author" id="author" name="author" value={form.author || ''} onChange={handleInputChange} required />
          <InputField label="ISBN" id="isbn" name="isbn" value={form.isbn || ''} onChange={handleInputChange} />
          <div className="grid grid-cols-2 gap-4">
            <InputField label="Year" id="published_year" name="published_year" type="number" value={form.published_year || ''} onChange={handleInputChange} />
            <InputField label="Genre" id="genre" name="genre" value={form.genre || ''} onChange={handleInputChange} />
          </div>
          <InputField label="Stock" id="stock" name="stock" type="number" value={form.stock || ''} onChange={handleInputChange} required />
          
          <div className="flex justify-end space-x-4 mt-6">
            <button type="button" onClick={handleCloseModal} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" disabled={appState.loading} className="flex items-center space-x-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:bg-indigo-400">
              <Save className="w-5 h-5" />
              <span>{appState.loading ? 'Saving...' : 'Save Book'}</span>
            </button>
          </div>
        </form>
      </Card>
    </div>
  );
  
  return (
    <>
      {isModalOpen && <BookModal />}
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-3xl font-bold text-gray-800">Book Management</h2>
        <button
          onClick={() => handleOpenModal()}
          className="flex items-center space-x-2 px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-md"
        >
          <Plus className="w-5 h-5" />
          <span>Add New Book</span>
        </button>
      </div>
      
      {/* Book Table for Management */}
      <div className="overflow-x-auto bg-white rounded-xl shadow-2xl">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBN</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {books.map((book) => (
              <tr key={book.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{book.title}</td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{book.author}</td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{book.isbn}</td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{book.stock}</td>
                <td className={`px-6 py-4 whitespace-nowrap text-sm text-center font-bold ${book.available_copies === 0 ? 'text-red-500' : 'text-green-600'}`}>
                  {book.available_copies}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-center space-x-3">
                  <button onClick={() => handleOpenModal(book)} className="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50 transition" title="Edit Book"><Settings className="w-5 h-5" /></button>
                  <button onClick={() => handleDelete(book.id, book.title)} disabled={!isAdmin} className={`p-1 rounded-full transition ${isAdmin ? 'text-red-600 hover:text-red-900 hover:bg-red-50' : 'text-gray-400 cursor-not-allowed'}`} title="Delete Book (Admin Only)"><Trash2 className="w-5 h-5" /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </>
  );
};

// --- Dashboard Component (Book List for all users) ---

const DashboardPage = ({ appState }) => {
  const { isLibrarianOrAdmin, notify, loading, setLoading, user, token } = appState;
  const [books, setBooks] = useState([]);
  const [error, setError] = useState(null);

  const fetchBooks = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      console.log('Fetching books from:', `${API_BASE_URL}/books`); // Debug log
      const res = await axios.get('/books', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      console.log('Books response:', res.data); // Debug log
      setBooks(Array.isArray(res.data) ? res.data : []);
    } catch (error) {
      console.error('Error fetching books:', error.response || error);
      const errorMessage = error.response?.data?.msg || 'Failed to load books.';
      setError(errorMessage);
      notify(errorMessage, 'error');
    } finally {
      setLoading(false);
    }
  }, [setLoading, notify, token]);

  useEffect(() => {
    if (token) {
      console.log('Token present, fetching books...'); // Debug log
      fetchBooks();
    } else {
      console.log('No token available'); // Debug log
    }
  }, [fetchBooks, token]);

  const handleBorrow = async (bookId, title) => {
    if (!token) {
        notify('Please log in to borrow a book.', 'error');
        return;
    }
    // NOTE: Using window.confirm() as a substitute for a custom modal in this environment.
    if (!window.confirm(`Confirm borrowing: "${title}"?`)) return;

    try {
        setLoading(true);
        const res = await axios.post(`/borrow/${bookId}`);
        notify(res.data.msg, 'success');
        fetchBooks(); // Refresh book list
    } catch (error) {
        notify(error.response?.data?.msg || 'Failed to borrow book.', 'error');
    } finally {
        setLoading(false);
    }
  };

  if (loading) {
    return <p className="text-center text-xl mt-12 text-indigo-600">Loading books...</p>;
  }
  
  if (error) {
    return (
      <div className="text-center py-8">
        <p className="text-xl text-red-600">{error}</p>
        <button
          onClick={fetchBooks}
          className="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
        >
          Retry Loading Books
        </button>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      {isLibrarianOrAdmin && <BookManager appState={appState} books={books} fetchBooks={fetchBooks} />}

      <h2 className={`text-3xl font-bold text-gray-800 ${isLibrarianOrAdmin ? 'mt-8 border-t pt-8' : ''}`}>
        Library Catalog
      </h2>

      {books.length === 0 ? (
        <p className="text-center text-gray-600 mt-8">No books available.</p>
      ) : (
        <div className="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {books.map((book) => (
            <Card key={book.id} className="flex flex-col justify-between">
              <h3 className="text-xl font-semibold text-indigo-700">{book.title}</h3>
              <p className="text-sm text-gray-600 italic">by {book.author}</p>
              <p className="text-xs mt-2 bg-gray-100 text-gray-700 px-2 py-1 rounded-md w-fit">
                Genre: {book.genre || 'N/A'}
              </p>
              <div className="mt-4 pt-4 border-t">
                <p className="text-sm font-medium text-gray-800">
                  Available: <span className={book.available_copies > 0 ? 'text-green-600' : 'text-red-500'}>
                    {book.available_copies} / {book.stock}
                  </span>
                </p>
                <button
                  onClick={() => handleBorrow(book.id, book.title)}
                  disabled={book.available_copies === 0 || loading || !token}
                  className="mt-3 w-full p-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition duration-150 disabled:bg-gray-400 disabled:cursor-not-allowed"
                >
                  {token ? (book.available_copies === 0 ? 'Out of Stock' : 'Borrow Book') : 'Log in to Borrow'}
                </button>
              </div>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
};

// --- Home Component ---

const HomePage = () => (
    <div className="container mx-auto px-4 py-16 text-center">
        <div className="max-w-3xl mx-auto">
            <h1 className="text-5xl font-extrabold text-gray-900 mb-4">
                Advanced Library Management System
            </h1>
            <p className="text-xl text-gray-600 mb-8">
                Your modern solution for managing books, users, and borrowing history with role-based access control and a beautiful user experience.
            </p>
            
            <div className="grid md:grid-cols-3 gap-6 mt-10">
                <FeatureCard icon={User} title="Role-Based Security" description="Separate access for Admin, Librarian, and standard Users using JWT." />
                <FeatureCard icon={BookOpen} title="Real-Time Catalog" description="View book availability and manage stock with real-time updates." />
                <FeatureCard icon={Settings} title="Advanced Profiles" description="Personalized user profiles with avatar uploads and borrowing history." />
            </div>
        </div>
    </div>
);

const FeatureCard = ({ icon: Icon, title, description }) => (
    <Card className="flex flex-col items-center text-center p-6 bg-indigo-50 border-t-4 border-indigo-600">
        <Icon className="w-10 h-10 text-indigo-600 mb-3" />
        <h3 className="text-xl font-semibold text-gray-800 mb-2">{title}</h3>
        <p className="text-gray-600">{description}</p>
    </Card>
);

// --- Main App Component ---

const App = () => {
  const appState = useAppState();
  const { route, user } = appState; // Extract user from appState

  const renderContent = useMemo(() => {
    switch (route) {
      case 'login':
        return <LoginPage appState={appState} />;
      case 'register':
        return <RegisterPage appState={appState} />;
      case 'profile':
        return appState.token ? <ProfilePage appState={appState} /> : <HomePage />;
      case 'dashboard':
        return appState.user?.role === 'admin' ? ( // Use appState.user instead of user
          <AdminDashboard appState={appState} />
        ) : (
          <DashboardPage appState={appState} />
        );
      case 'home':
      default:
        return <HomePage />;
    }
  }, [route, appState]); // Add appState to dependencies

  return (
    <div className="min-h-screen bg-gray-50 font-sans">
      <Header user={appState.user} setRoute={appState.setRoute} logout={appState.logout} />
      <main className="pb-16">
        {renderContent}
      </main>
      <Notification message={appState.message} />
    </div>
  );
};

// Update AdminDashboard to use appState directly
const AdminDashboard = ({ appState }) => {
  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-semibold">Admin Dashboard</h1>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <DashboardCard
          title="Manage Books"
          icon={<BookOpen className="h-8 w-8" />}
          onClick={() => appState.setRoute('books')}
        />
        <DashboardCard
          title="Manage Users"
          icon={<User className="h-8 w-8" />}
          onClick={() => appState.setRoute('users')}
        />
        <DashboardCard
          title="Settings"
          icon={<Settings className="h-8 w-8" />}
          onClick={() => appState.setRoute('settings')}
        />
      </div>
    </div>
  );
};

const DashboardCard = ({ title, icon, onClick }) => {
  return (
    <button
      onClick={onClick}
      className="p-6 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 text-left w-full"
    >
      <div className="flex items-center gap-4">
        {icon}
        <h3 className="text-lg font-medium text-gray-900">{title}</h3>
      </div>
    </button>
  );
};

export default App;
