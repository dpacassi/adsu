# Automatic Drupal Security Updates

This is a **WIP** proof of concept that automated updates for Drupal 8 are very well possible.

## Installation
- Clone or download
- Copy the `.env.example` file to `.env`
- **Important:** This is on a WIP proof of concept state, don't use it on live sites!
- Run `update.sh`

## Current workflow
The current idea is that next to a production/staging/development environment, we create
an "update" environment.  
Like the other environments, this update environment has it's own URL,
database, code base.  
The code base is checked out from the `master` branch (or whichever branch you use for production) and
the script/the host where the script is running has `git push` rights to the repository.

We set up a cron job which runs the update script every x hours/minutes.  
The script basically does the following:

1. Retrieve a list of all installed projects (modules/themes)
2. Check if security updates are available
3. If none: Nothing to do :-), Otherwise:
4. Sync database & files from production to "update" environment
5. Run updates
6. Run tests
7. Commit and push changes
8. Enjoy a coffee. CI/CD does the rest.

## Contribution
- Pull requests are **welcome**!
- Question? Feedback? Tensions? Please create an [issue](https://github.com/dpacassi/adsu/issues/new)!

## License
```
MIT License

Copyright (c) 2019 David Pacassi Torrico

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
