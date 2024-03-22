function dispatchCustomEvent(name, dataObject) {
    const customEvent = new CustomEvent(
        `${name}`,
        {
            bubbles: true,
            cancelable: true,
            detail: dataObject
        }
    );
    document.dispatchEvent(customEvent);
}

export { dispatchCustomEvent };