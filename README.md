# notesmith
Turn your iPhone into your own personal impression pad!

## SETTING UP

If you want to add this to a new website, simply save all the files on to your web server's main public folder.

If you want to add this to an existing website, simply create a subfolder in your main public folder called "notesmith" and save all the files into this folder.

Either way, once done, open Safari on your iPhone and navigate to the full address of the index file, e.g. https://www.mywebsite.com/notesmith/index.php.

When the page loads, click on the sharing icon in the browser and select "Add to Home Screen". Ensure you have "Open as Web App" checked to stop the URL bar from showing.

You should now have an icon on your homescreen that looks like the iOS Notes app icon.

## MYSTERSMITH

Open **MysterSmith** and navigate to **Settings** > **WEB-POLLING**. Click **Configure** next to **WEB data sources**, scroll to the bottom to the **CUSTOM data sources** section and add a custom data source. Add the full URL from above (e.g. https://www.mywebsite.com/notesmith/index.php), tick both **Enable anti-cache** and **JSON format**, and type **noteData** into the **Display field(s)** box. Then click **Save and close**. Back in the **WEB-POLLING** section, untick **Show received data on top**.

In you want the peek to go to your Apple Watch, go in to **EXTERNAL DISPLAY** in the main settings page and tick **Use Apple Watch** and set **Apple watch display** to **enable**.

When you're ready to perform, just go go the **Perform** page and tap on **WEB-POLLING**. Then open the fake Notes app.

That's it. Anything typed on the fake Notes page will now be sent to your PeekSmith and/or Apple Watch.

## NOTES APP INSTRUCTIONS

Tap the left arrow at the top-left of the page to clear anything and start again.

Tap the top right icons to switch between light and dark modes.