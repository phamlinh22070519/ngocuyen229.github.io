from flask import Blueprint, request, jsonify, current_app
from flask_jwt_extended import jwt_required, get_jwt_identity, get_jwt, create_access_token
from functools import wraps
from werkzeug.utils import secure_filename
from datetime import datetime, timedelta
from . import db, bcrypt
from .models import User, Book, Borrowing

bp = Blueprint('api', __name__)

# Add health check endpoint
@bp.route('/', methods=['GET'])
def health_check():
    """Health check endpoint with API information"""
    return jsonify({
        "status": "healthy",
        "message": "Library Management System API is running",
        "version": "1.0.0",
        "timestamp": datetime.utcnow().isoformat(),
        "endpoints": {
            "auth": {
                "register": "/api/register",
                "login": "/api/login"
            },
            "books": {
                "list": "/api/books",
                "create": "/api/books",
                "update": "/api/books/<id>",
                "delete": "/api/books/<id>"
            },
            "profile": {
                "get": "/api/profile",
                "avatar": "/api/profile/avatar"
            }
        }
    }), 200

# --- Utility Decorators for Role-Based Access Control ---
def role_required(role_names):
    def wrapper(fn):
        @wraps(fn)  # Add this to preserve the original function's metadata
        @jwt_required()
        def decorated(*args, **kwargs):
            current_user = get_jwt()
            if current_user.get('role') in role_names:
                return fn(*args, **kwargs)
            return jsonify(msg=f"Access denied: {role_names[0].capitalize()} role required"), 403
        return decorated
    return wrapper

admin_required = role_required(['admin'])
librarian_or_admin_required = role_required(['librarian', 'admin'])

# --- Authentication Routes ---
@bp.route('/register', methods=['POST'])
def register():
    data = request.get_json()
    if User.query.filter_by(email=data['email']).first():
        return jsonify({"msg": "Email already registered"}), 400
    
    hashed_password = bcrypt.generate_password_hash(data['password']).decode('utf-8')
    new_user = User(
        username=data['username'],
        email=data['email'],
        password_hash=hashed_password,
        role=data.get('role', 'user')
    )
    
    db.session.add(new_user)
    db.session.commit()
    return jsonify({"msg": "User created successfully"}), 201

@bp.route('/login', methods=['POST'])
def login():
    data = request.get_json()
    user = User.query.filter_by(email=data['email']).first()
    
    if user and bcrypt.check_password_hash(user.password_hash, data['password']):
        access_token = create_access_token(identity=user)
        return jsonify({"token": access_token, "user": user.to_dict()}), 200
    return jsonify({"msg": "Invalid credentials"}), 401

# --- Books Routes ---
@bp.route('/books', methods=['GET'])
def get_books():
    try:
        books = Book.query.all()
        return jsonify([book.to_dict() for book in books]), 200
    except Exception as e:
        print(f"Error fetching books: {str(e)}")  # Debug log
        return jsonify({"error": "Failed to fetch books"}), 500

@bp.route('/books', methods=['POST'])
@librarian_or_admin_required
def add_book():
    data = request.get_json()
    new_book = Book(**data)
    db.session.add(new_book)
    db.session.commit()
    return jsonify(new_book.to_dict()), 201

@bp.route('/profile', methods=['GET'])
@jwt_required()
def get_profile():
    current_user = get_jwt_identity()
    user = User.query.get_or_404(current_user)
    return jsonify(user.to_dict()), 200

@bp.route('/books/<int:book_id>', methods=['PUT'])
@librarian_or_admin_required
def update_book(book_id):
    book = Book.query.get_or_404(book_id)
    data = request.get_json()
    
    for key, value in data.items():
        if hasattr(book, key):
            setattr(book, key, value)
    
    db.session.commit()
    return jsonify(book.to_dict()), 200

@bp.route('/books/<int:book_id>', methods=['DELETE'])
@admin_required
def delete_book(book_id):
    book = Book.query.get_or_404(book_id)
    db.session.delete(book)
    db.session.commit()
    return '', 204
