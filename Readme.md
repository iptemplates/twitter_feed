## Twitter feed plugin for ImpressPages CMS

With this plugin help you will be able include in your site Twitter feed widget with ease.

## Installation

1. Upload "standard" folder to "ip_plugins/" directory
2. Login to administration area
3. Go to "Developer -> Modules" and press "install" on Twitter feed plugin.

###Important - to get your public timeline data from Twitter you need to:

1. Register at https://dev.twitter.com/apps/new and create a new app.
2. After registering, fill in App name, e.g. "My Best Twitter App", description, e.g "My Twitter App", and write the address of your website. Check "I agree" next to their terms of service and click "create your Twitter application"
3. After this you app will be created. Click "Create my access token" and you should see at the bottom "access token" and "access token secret". Refresh the page if you don't see them.
4. Copy to widget settings "Consumer key", "Consumer secret", "Access token" and "Access secret" located "Standard -> Configuration -> Twitter Feed -> Login credentials".

## FAQ

Q: Why do I have to trouble with all of this?
A: Twitter is removing access for all unauthorized requests, so every extension which wants to connect to Twitter must use authentication, otherwise it will stop working (many already have).

Q: My widget doesn't work!
A: Make sure that you have copied the correct keys. If widget type is set to timeline, make sure you chose a valid Twitter username. If widget type is set to search, Twitter may return error if search query is extremely complex.

Q: Do you cache results?
A: Yes, but you should almost always see the latest tweets. Widget will always try to get the latest tweets and save them to a cache. In case of a problem, tweets will be retrived from the cache. For high traffic sites (more than 10.000 visits per day), you may occasionally get tweets from the cache, as Twitter doesn't allow more than 180 requests per 15 minutes. If you use more requests than allowed, widget will display latest saved tweets from the cache, until new 15 minute window opens.

## Usage

Use it as a usual widget :)

## Copyright / License

The MIT License

Copyright 2013 JSC "Insightio" ( http://www.insightio.lt / http://www.insightio.co.uk )

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
