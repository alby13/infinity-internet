<h1 align="center">Infinity Internet</h1>

Infinity Internet is a PHP web application that generates HTML and CSS webpages based on user input. It utilizes the Cerebras API to create dynamic web content tailored to user descriptions.

Advanced websites are programmed instantly, saved on the web server, and served to the user seamlessly.

<h2 align="center">Features</h2>

- Generate unique web pages with HTML and CSS based on user prompts.
- Automatically logs actions and errors for debugging in .
- Rate limiting to prevent abuse (one request every 5 seconds).
- Automatically deletes old generated pages after 10 minutes.

<h1 align="center">About Cerebras Inference</h1>

Cerebras Inference currently holds the record for the fastest AI Inference speed on the internet (As of August 28, 2024).

This program is designed to use the "Instant AI" of Cerebras API. Large Language Model AI Content Generating

Cerebras Utilizes the Open-Source AI Model Llama 3.1 70Billion Parameter.

https://www.cerebras.ai

<h2 align="center">How do I set Infinity Internet up on my web server?</h2>

Step 1. Configure the API key by editing <code>config.php</code> and Copy and Paste your API Key inside.

Step 2. Set up your web server: Ensure your web server is configured to serve PHP files. You can use Apache, Nginx, or any other server that supports PHP.

Step 3. Directory permissions: Make sure the generated-pages directory is writable by the web server: 

mkdir generated-pages
chmod 775 generated-pages



<h2 align="center">How does Infinity Interent work?</h2>


<h2 align="center">How does the user use Infinity Internet?</h2>


# Truth Open Source License (TOSL)

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
