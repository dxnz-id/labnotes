### **Install Dependencies**  
```bash
composer install
npm install
```

### **Environment Configuration**  
1. Copy the `.env.example` file and rename it to `.env`.  
2. Configure the environment file to match your database and application settings.  
3. Link the storage:  
   ```bash
   php artisan storage:link
   ```
4. Generate an application key:  
   ```bash
   php artisan key:generate
   ```

### **Storage Setup**  
```bash
php artisan storage:link
```
### **Database Setup**  
```bash
php artisan migrate
php artisan db:seed
```

### **Start the Application**  
```bash
composer run dev
```

Open your browser and visit:  
```
http://localhost:8000/admin/login
```

### **Default Login Credentials**  
You can log in using the following accounts:  

| Email                 | Password      |
|-----------------------|--------------|
| user@example.com      | labnotes123  |

--- 

## **Contact**  
For any inquiries, feel free to reach out!

<a href="https://www.ko-fi.com/dxnzid">
<img src="https://cdn.ko-fi.com/cdn/kofi3.png?v=3" width="160" alt="ko-fi" />
</a>
