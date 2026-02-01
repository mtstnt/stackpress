# Personal TODOs
[x] Base Proof-of-concept that this thing can be done.  
[x] Refactor to use OOP structure, to make adding features easier later on.  
[] Adjust the folder structure to:  
    - Disallow direct access on framework code.  
    - Allow drop-in folder structure for admin & public pages (means build files are in the root).  
        - Or should we use .htaccess to rewrite for those?  
[] Data feature.  
    - Dynamic data definition, creates a new JSON file and a new CRUD entity.  
    - Create with different form types: text, integer, double, asset upload, etc.  
[] Actions feature.  
    - Dynamic data creation from API endpoint.  
    - The API can be used by the FE for form submissions.  
    - Implement CSRF and Captcha.  
[] Site Settings feature    
    - Key-Value pair of information. Can be of any type.  
    - Can be queried in the view.  
[] Pages feature  
    - Drag and drop page builder in the Admin, stores in a JSON format somewhere, then during build, it can be hydrated.  
[] Security Audit  
    - Perform security audit to find potential issues.  