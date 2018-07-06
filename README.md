# Julia

At the moment of writing this theme has two variations: **Julia** and **Felt**.
The code for all the variation is kept on the same branch and generating the desired variation theme package is solely based on the build tasks.

## Dependencies
Make sure you have installed **Node.js** and **npm** then run the following command to install all dependencies.
```
npm install
```

## Compiling
These are watch tasks used for development. They also have to be run at least once before building the theme package to make sure that the proper styles, scripts and php configs for the desired variation are used.

#### TypeScript and SCSS compiling
```
npm run felt
```
#### Sync PHP variation files
```
gulp watch-variation --variation felt
```

## Build
```
gulp zip --variation felt
```
