<h1 align="center">Infinity Internet</h1>

Infinity Internet is a PHP web application that generates HTML and CSS webpages based on user input. It utilizes the Cerebras API to create dynamic web content tailored to user descriptions.

Advanced websites are programmed instantly, saved on the web server, and served to the user seamlessly.

<h2 align="center">Features</h2>

- Generate unique websites that are styled with CSS based on user prompts.
- Automatically logs actions and errors for debugging in <code>infinity_log.txt</code>.
- Rate limiting to prevent overloading (one request every 5 seconds).
- Automatically deletes old generated pages after 10 minutes.

<h1 align="center">About Cerebras Inference</h1>

Cerebras Inference currently holds the record for the fastest AI Inference speed on the internet (As of August 28, 2024).

This program is designed to use the "Instant AI" of Cerebras API. Large Language Model AI Content Generating

Cerebras Utilizes the Open-Source AI Model Llama 3.1 70Billion Parameter.

https://www.cerebras.ai

<h2 align="center">How do I set Infinity Internet up on my web server?</h2>

**Step 1.  Configure the API Key**

Configure the API key by editing <code>config.php</code> and Copy and Paste your API Key inside.

**Step 2.  Install Guzzle Library**

Before setting up the application, you need to install Guzzle, a PHP HTTP client that the application uses to interact with the Cerebras API. You can install Guzzle using Composer. If you haven't installed Composer yet, you can download it from [getcomposer.org](https://getcomposer.org/).

**Step 3. Set up your web server**

Ensure your web server is configured to serve PHP files. You can use Apache, Nginx, or any other server that supports PHP.

**Step 4. Set up the "generated-pages" directory (Optional)**

Directory permissions: Make sure the generated-pages directory is writable by the web server: 

Create "generated-pages" directory through FTP and change the file permissions to 775, or for Linux...
~~~
mkdir generated-pages
chmod 775 generated-pages
~~~

During my testing on Apache the PHP scripts set up the folders, generated the files, and set the file permissions as needed automatically.

<h2 align="center">How does Infinity Internet work?</h2>

The Infinity Internet application is a PHP-based web application that generates HTML and CSS web pages based on user input. It interacts with the Cerebras API to create content dynamically. The application is structured to handle user sessions, manage API requests, and log activities for debugging purposes.

## Technical Components

1. **PHP and Composer**:
   - The application is built using PHP, a server-side scripting language. Composer is used for dependency management, allowing the application to utilize external libraries like Guzzle for making HTTP requests.

2. **Cerebras API**:
   - The application communicates with the Cerebras API to generate web content. The API key is stored in a configuration file (`config.php`), which is required for authentication when making requests to the API.

3. **User Sessions**:
   - The application uses PHP sessions to track user interactions. Each user is assigned a unique identifier (`user_id`) stored in the session, which helps in organizing generated content.

4. **Generating Web Pages**:
   - When a user submits a prompt through the web form, the application processes the input and sends a request to the Cerebras API. The request includes:
     - A system message that instructs the API on how to generate the content (e.g., creating a full HTML page with CSS).
     - The user's prompt, which describes the desired website.
   - The API responds with generated HTML and CSS, which is then saved to a file in a user-specific directory.

5. **Error Handling and Logging**:
   - The application includes error handling mechanisms to manage API request failures and unexpected responses. Errors are logged to a file (`debug_log.txt`) for troubleshooting.
   - The application implements a retry mechanism, attempting to regenerate the page up to three times if an error occurs.

6. **Rate Limiting**:
   - To prevent abuse, the application enforces a rate limit of one request every 5 seconds per user. This is managed by storing the timestamp of the last request in the session.

7. **Deleting Old Pages**:
   - The application periodically cleans up old generated pages. Any files older than 10 minutes are deleted to save storage space and keep the directory organized.

8. **Frontend Interface**:
   - The user interface is built using HTML and CSS. It includes a form for user input and displays error messages if any issues arise during the page generation process.
   - JavaScript can be used to show a loading indicator when the form is submitted, enhancing the user experience.

## Workflow

1. **User Input**:
   - The user enters a description of the desired website in a text input field and submits the form.

2. **Processing the Request**:
   - Upon form submission, the application checks the rate limit and sanitizes the user input.
   - A request is sent to the Cerebras API with the user's prompt and the system message.

3. **Receiving the Response**:
   - The application processes the API response, extracting the generated HTML and CSS.
   - If successful, the content is saved to a uniquely named file in a directory specific to the user.

4. **Redirecting the User**:
   - The user is redirected to the newly generated page, where they can view the content.

5. **Error Handling**:
   - If any errors occur during the process, appropriate messages are displayed to the user, and the errors are logged for further analysis.

The Infinity Internet application is designed to be user-friendly while maintaining robust error handling and logging capabilities, making it suitable for users looking to dream up web content based on their descriptions.

<h2 align="center">How does the user use Infinity Internet?</h2>

1. Open your web browser and navigate to the URL where your application is hosted (e.g., http://localhost/infinity-internet).

2. Enter a description of the website you want to generate in the input box.

3. Click the "To Infinity" button to generate the web page.

You will be redirected to the newly generated page once it is ready. If there are any errors, they will be displayed on the main page. There is a server log called <code>infinity_log.txt</code> as well.


### Acknowledgments
Guzzle HTTP client used to interact with the Cerebras API. http://guzzlephp.org/

# True Open Source License (TrueOSL)

Version 1.0

1. Permissions

You are free to use, modify, distribute, and sublicense the code and content of the "Infinity Internet" project (the "Software") in any manner you choose, without any restrictions.

2. Attribution

Attribution to the original author (alby13) of the Software is appreciated but not required.

3. Disclaimer of Warranty

The Software is provided "as is," without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose, and non-infringement. In no event shall the original author(s) be liable for any claim, damages, or other liability, whether in an action of contract, tort, or otherwise, arising from, out of, or in connection with the Software or the use or other dealings in the Software.

4. Responsible Use

Developers and Users of the Software are encouraged to program, deploy, and use it responsibly, especially in the context of AI-generated content. The original author does not endorse any specific use of the Software that may lead to harm or unethical outcomes.

5. Termination

The license may be terminated if the Software is used in a manner that is deemed harmful, malicious, or unethical by the original author. Upon termination, all rights granted under this license will cease immediately.

6. No Contribution Terms

There are no specific terms or agreements required for contributions to the Software. Contributors are encouraged to share their improvements and modifications freely.

7. Open Source Philosophy

This license is designed to promote the truest sense of open source, allowing for maximum freedom and collaboration while maintaining a commitment to responsible use.

Written on August 28, 2024 - A copy of this license is encouraged to be included with your software, but is not required.
