# UTM tagged links

Use these whenever linking to the sites from a social account, so Google
Analytics shows which account sent each visitor.

## Bio / profile links

### YouTube @SteveHazel
- https://pianocheetah.app/?utm_source=youtube&utm_medium=social&utm_campaign=bio
- https://shaz.app/?utm_source=youtube&utm_medium=social&utm_campaign=bio

### TikTok @stephenhazel98
- https://pianocheetah.app/?utm_source=tiktok&utm_medium=social&utm_campaign=bio
- https://shaz.app/?utm_source=tiktok&utm_medium=social&utm_campaign=bio

### X @SHazelIsOk
- https://pianocheetah.app/?utm_source=x&utm_medium=social&utm_campaign=bio
- https://shaz.app/?utm_source=x&utm_medium=social&utm_campaign=bio

### Instagram @stephenhazelisannoyed
- https://pianocheetah.app/?utm_source=instagram&utm_medium=social&utm_campaign=bio
- https://shaz.app/?utm_source=instagram&utm_medium=social&utm_campaign=bio

### Facebook (stephen.hazel / PianoCheetah)
- https://pianocheetah.app/?utm_source=facebook&utm_medium=social&utm_campaign=bio
- https://shaz.app/?utm_source=facebook&utm_medium=social&utm_campaign=bio

## Video descriptions and posts

Same link, but swap `utm_campaign=bio` for:
- `utm_campaign=video` - YouTube, TikTok
- `utm_campaign=post`  - X, Instagram, Facebook

e.g.
https://pianocheetah.app/?utm_source=youtube&utm_medium=social&utm_campaign=video

## Linking to a specific page

Keep the tag, change only the path:
https://shaz.app/tunez/?utm_source=tiktok&utm_medium=social&utm_campaign=bio

## Rules

- All lowercase - `YouTube` and `youtube` count as two different sources.
- Only use tagged links off-site.  Never on links between pages of your
  own sites, or Analytics loses track of where the visitor came from.

## Where to see results in Google Analytics

Reports > Acquisition > Traffic acquisition, then set the column to
**Session source / medium** (rows like `youtube / social`,
`tiktok / social`).  Switch it to **Session campaign** to compare bio
links against video / post links.
