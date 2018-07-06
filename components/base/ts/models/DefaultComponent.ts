export interface Component {
  destroy: () => void;
  bindEvents: () => void;
}

export abstract class BaseComponent implements Component {
  constructor() {

  }

  public abstract destroy(): void;

  public abstract bindEvents(): void;
}
