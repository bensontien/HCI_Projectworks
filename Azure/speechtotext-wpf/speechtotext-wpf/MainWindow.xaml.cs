
namespace MicrosoftSpeechSDKSamples.WpfSpeechRecognitionSample
{
    using System;
    using System.Globalization;
    using System.ComponentModel;
    using System.Diagnostics;
    using System.IO;
    using System.Media;
    using System.Runtime.CompilerServices;
    using System.Threading.Tasks;
    using System.Windows;
    using System.Windows.Controls;
    using Forms = System.Windows.Forms;
    using System.IO.IsolatedStorage;

    using Microsoft.CognitiveServices.Speech;
    using Microsoft.CognitiveServices.Speech.Audio;

    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window, INotifyPropertyChanged
    {
        #region Properties
        string ans;
        /// <summary>
        /// True, if audio source is mic
        /// </summary>
        public bool UseMicrophone { get; set; }

        /// <summary>
        /// True, if audio source is audio file
        /// </summary>
        public bool UseFileInput { get; set; }

        /// <summary>
        /// Only baseline model used for recognition
        /// </summary>
        public bool UseBaseModel { get; set; }

        /// <summary>
        /// Only custom model used for recognition
        /// </summary>
        public bool UseCustomModel { get; set; }

        /// <summary>
        /// Both models used for recognition
        /// </summary>
        public bool UseBaseAndCustomModels { get; set; }

        /// <summary>
        /// Gets or sets Subscription Key
        /// </summary>
        public string SubscriptionKey
        {
            get
            {
                return this.subscriptionKey;
            }

            set
            {
                this.subscriptionKey = value?.Trim();
                this.OnPropertyChanged<string>();
            }
        }

        /// <summary>
        /// Gets or sets region name of the service
        /// </summary>
        public string Region { get; set; }

        /// <summary>
        /// Gets or sets recognition language
        /// </summary>
        public string RecognitionLanguage { get; set; }

        /// <summary>
        /// Gets or sets endpoint ID of the custom model
        /// </summary>
        public string CustomModelEndpointId
        {
            get
            {
                return this.endpointId;
            }

            set
            {
                this.endpointId = value?.Trim();
                this.OnPropertyChanged<string>();
            }
        }

        // Private properties
        private const string defaultLocale = "en-US";
        private string endpointId;
        private string subscriptionKey;
        private const string endpointIdFileName = "CustomModelEndpointId.txt";
        private const string subscriptionKeyFileName = "SubscriptionKey.txt";
        private string wavFileName;

        private TaskCompletionSource<int> stopBaseRecognitionTaskCompletionSource;
        private TaskCompletionSource<int> stopCustomRecognitionTaskCompletionSource;

        #endregion

        /// <summary>
        /// For this app there are two recognizers, one with the baseline model (Base), one with CRIS model (Custom)
        /// </summary>
        enum RecoType
        {
            Base = 1,
            Custom = 2
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="MainWindow"/> class.
        /// </summary>
        public MainWindow()
        {
            this.InitializeComponent();
            this.Initialize();
        }

        /// <summary>
        /// Initializes a fresh audio session.
        /// </summary>
        private void Initialize()
        {
            this.UseMicrophone = false;
            this.UseFileInput = true;

            this.UseBaseModel = true;

            // Set the default values for UI
            this.fileInputRadioButton.IsChecked = true;
            this.basicRadioButton.IsChecked = true;
            this.stopButton.IsEnabled = false;

            this.SubscriptionKey = this.GetValueFromIsolatedStorage(subscriptionKeyFileName);
            this.CustomModelEndpointId = this.GetValueFromIsolatedStorage(endpointIdFileName);
        }

        /// <summary>
        /// Handles the Click event of the StartButton:
        /// Disables Settings Panel in UI to prevent Click Events during Recognition
        /// Checks if keys are valid
        /// Plays audio if input source is a valid audio file
        /// Triggers Creation of specified Recognizers
        /// </summary>
        /// <param name="sender">The source of the event.</param>
        /// <param name="e">The <see cref="RoutedEventArgs"/> instance containing the event data.</param>
        private void StartButton_Click(object sender, RoutedEventArgs e)
        {
            this.startButton.IsEnabled = false;
            this.stopButton.IsEnabled = true;
            this.radioGroup.IsEnabled = false;
            this.optionPanel.IsEnabled = false;
            this.LogRecognitionStart(this.customModelLogText, this.customModelCurrentText);
            this.LogRecognitionStart(this.baseModelLogText, this.baseModelCurrentText);
            wavFileName = "";

            this.Region = ((ComboBoxItem)regionComboBox.SelectedItem).Tag.ToString();
            this.RecognitionLanguage = ((ComboBoxItem)languageComboBox.SelectedItem).Tag.ToString();

            if (!AreKeysValid())
            {
                if (this.UseBaseModel)
                {
                    MessageBox.Show("Subscription Key is wrong or missing!");
                    this.WriteLine(this.baseModelLogText, "--- Error : Subscription Key is wrong or missing! ---");
                }
                

                this.EnableButtons();
                return;
            }

            if (!this.UseMicrophone)
            {
                wavFileName = GetFile();
                if (wavFileName.Length <= 0) return;
                Task.Run(() => this.PlayAudioFile());
            }


            if (this.UseBaseModel)
            {
                stopBaseRecognitionTaskCompletionSource = new TaskCompletionSource<int>();
                Task.Run(async () => { await CreateBaseReco().ConfigureAwait(false); });
            }
        }


        /// <summary>
        /// Handles the Click event of the StopButton:
        /// Stops Recognition and enables Settings Panel in UI
        /// </summary>
        /// <param name="sender">The source of the event.</param>
        /// <param name="e">The <see cref="RoutedEventArgs"/> instance containing the event data.</param>
        private void StopButton_Click(object sender, RoutedEventArgs e)
        {
            this.stopButton.IsEnabled = false;
            if (this.UseBaseModel)
            {
                stopBaseRecognitionTaskCompletionSource.TrySetResult(0);
            }
            

            EnableButtons();
        }

        /// <summary>
        /// Creates Recognizer with baseline model and selected language:
        /// Creates a config with subscription key and selected region
        /// If input source is audio file, creates recognizer with audio file otherwise with default mic
        /// Waits on RunRecognition
        /// </summary>
        private async Task CreateBaseReco()
        {
            // Todo: suport users to specifiy a different region.
            var config = SpeechConfig.FromSubscription(this.SubscriptionKey, this.Region);
            config.SpeechRecognitionLanguage = this.RecognitionLanguage;

            SpeechRecognizer basicRecognizer;
            if (this.UseMicrophone)
            {
                using (basicRecognizer = new SpeechRecognizer(config))
                {
                    await this.RunRecognizer(basicRecognizer, RecoType.Base, stopBaseRecognitionTaskCompletionSource).ConfigureAwait(false);
                }
            }
            else
            {
                using (var audioInput = AudioConfig.FromWavFileInput(wavFileName))
                {
                    using (basicRecognizer = new SpeechRecognizer(config, audioInput))
                    {
                        await this.RunRecognizer(basicRecognizer, RecoType.Base, stopBaseRecognitionTaskCompletionSource).ConfigureAwait(false);
                    }
               }
            }
        }

        /// <summary>
        /// Creates Recognizer with custom model endpointId and selected language:
        /// Creates a config with subscription key and selected region
        /// If input source is audio file, creates recognizer with audio file otherwise with default mic
        /// Waits on RunRecognition
        /// </summary>

        /// <summary>
        /// Subscribes to Recognition Events
        /// Starts the Recognition and waits until final result is received, then Stops recognition
        /// </summary>
        /// <param name="recognizer">Recognizer object</param>
        /// <param name="recoType">Type of Recognizer</param>
        ///  <value>
        ///   <c>Base</c> if Baseline model; otherwise, <c>Custom</c>.
        /// </value>
        private async Task RunRecognizer(SpeechRecognizer recognizer, RecoType recoType, TaskCompletionSource<int> source)
        {
            //subscribe to events
            bool isChecked = false;
            this.Dispatcher.Invoke(() =>
            {
                isChecked = this.immediateResultsCheckBox.IsChecked == true;
            });

            EventHandler<SpeechRecognitionEventArgs> recognizingHandler = (sender, e) => RecognizingEventHandler(e, recoType);
            if (isChecked)
            {
                recognizer.Recognizing += recognizingHandler;
            }

            EventHandler<SpeechRecognitionEventArgs> recognizedHandler = (sender, e) => RecognizedEventHandler(e, recoType);
            EventHandler<SpeechRecognitionCanceledEventArgs> canceledHandler = (sender, e) => CanceledEventHandler(e, recoType, source);
            EventHandler<SessionEventArgs> sessionStartedHandler = (sender, e) => SessionStartedEventHandler(e, recoType);
            EventHandler<SessionEventArgs> sessionStoppedHandler = (sender, e) => SessionStoppedEventHandler(e, recoType, source);
            EventHandler<RecognitionEventArgs> speechStartDetectedHandler = (sender, e) => SpeechDetectedEventHandler(e, recoType, "start");
            EventHandler<RecognitionEventArgs> speechEndDetectedHandler = (sender, e) => SpeechDetectedEventHandler(e, recoType, "end");

            recognizer.Recognized += recognizedHandler;
            recognizer.Canceled += canceledHandler;
            recognizer.SessionStarted += sessionStartedHandler;
            recognizer.SessionStopped += sessionStoppedHandler;
            recognizer.SpeechStartDetected -= speechStartDetectedHandler;
            recognizer.SpeechEndDetected -= speechEndDetectedHandler;

            //start,wait,stop recognition
            await recognizer.StartContinuousRecognitionAsync().ConfigureAwait(false);
            await source.Task.ConfigureAwait(false);
            await recognizer.StopContinuousRecognitionAsync().ConfigureAwait(false);

            this.EnableButtons();

            // unsubscribe from events
            if (isChecked)
            {
                recognizer.Recognizing -= recognizingHandler;
            }
            recognizer.Recognized -= recognizedHandler;
            recognizer.Canceled -= canceledHandler;
            recognizer.SessionStarted -= sessionStartedHandler;
            recognizer.SessionStopped -= sessionStoppedHandler;
            recognizer.SpeechStartDetected -= speechStartDetectedHandler;
            recognizer.SpeechEndDetected -= speechEndDetectedHandler;
        }

        #region Recognition Event Handlers

        /// <summary>
        /// Logs intermediate recognition results
        /// </summary>
        private void RecognizingEventHandler(SpeechRecognitionEventArgs e, RecoType rt)
        {
            var log = (rt == RecoType.Base) ? this.baseModelLogText : this.customModelLogText;
            this.WriteLine(log, "Intermediate result: {0} ", e.Result.Text);
        }

        /// <summary>
        /// Logs the final recognition result
        /// </summary>
        private void RecognizedEventHandler(SpeechRecognitionEventArgs e, RecoType rt)
        {
            TextBox log;
            
                log = this.baseModelLogText;
                string s1 = "What is the best University?";
         

                this.SetCurrentText(this.baseModelCurrentText, e.Result.Text);

                if (s1.Equals(e.Result.Text) ==true)
                {
                    this.SetCurrentText(this.customModelCurrentText, "NCU");

                ans = "NCU";
                SynthesisToSpeakerAsync().Wait();
                SoundPlayer player = new SoundPlayer();
                player.SoundLocation = @"D:\HCI\2019summer\HCI_homework\Azure\speechtotext-wpf\speechtotext-wpf\sample.wav";
                player.Load();
                player.Play();
                }

                else
                {

                ans = "What are you talking about?";
                this.SetCurrentText(this.customModelCurrentText, "What are you talking about?");
                SoundPlayer player = new SoundPlayer();
                SynthesisToSpeakerAsync().Wait();
                player.SoundLocation = @"D:\HCI\2019summer\HCI_homework\Azure\speechtotext-wpf\speechtotext-wpf\sample2.wav";
                player.Load();
                player.Play();

            }



         

            //this.WriteLine(log);
            //this.WriteLine(log, $" --- Final result received. Reason: {e.Result.Reason.ToString()}. --- ");
            if (!string.IsNullOrEmpty(e.Result.Text))
            {
                this.WriteLine(log, e.Result.Text);
            }

            // if access to the JSON is needed it can be obtained from Properties
            string json = e.Result.Properties.GetProperty(PropertyId.SpeechServiceResponse_JsonResult);

        }

        /// <summary>
        /// Logs Canceled events
        /// And sets the TaskCompletionSource to 0, in order to trigger Recognition Stop
        /// </summary>
        private void CanceledEventHandler(SpeechRecognitionCanceledEventArgs e, RecoType rt, TaskCompletionSource<int> source)
        {
            var log = (rt == RecoType.Base) ? this.baseModelLogText : this.customModelLogText;
            source.TrySetResult(0);
            this.WriteLine(log, "--- recognition canceled ---");
            this.WriteLine(log, $"CancellationReason: {e.Reason.ToString()}. ErrorDetails: {e.ErrorDetails}.");
            this.WriteLine(log);
        }

        /// <summary>
        /// Session started event handler.
        /// </summary>
        private void SessionStartedEventHandler(SessionEventArgs e, RecoType rt)
        {
            var log = (rt == RecoType.Base) ? this.baseModelLogText : this.customModelLogText;
            this.WriteLine(log, String.Format(CultureInfo.InvariantCulture, "Speech recognition: Session started event: {0}.", e.ToString()));
        }

        /// <summary>
        /// Session stopped event handler. Set the TaskCompletionSource to 0, in order to trigger Recognition Stop
        /// </summary>
        private void SessionStoppedEventHandler(SessionEventArgs e, RecoType rt, TaskCompletionSource<int> source)
        {
            var log = (rt == RecoType.Base) ? this.baseModelLogText : this.customModelLogText;
            this.WriteLine(log, String.Format(CultureInfo.InvariantCulture, "Speech recognition: Session stopped event: {0}.", e.ToString()));
            source.TrySetResult(0);
        }

        private void SpeechDetectedEventHandler(RecognitionEventArgs e, RecoType rt, string eventType)
        {
            var log = (rt == RecoType.Base) ? this.baseModelLogText : this.customModelLogText;
            this.WriteLine(log, String.Format(CultureInfo.InvariantCulture, "Speech recognition: Speech {0} detected event: {1}.",
                eventType, e.ToString()));
        }

        #endregion

        #region Helper Functions
        /// <summary>
        /// Retrieves Key from File
        /// </summary>
        /// <param name="fileName">Name of file which contains key</param>
        private string GetValueFromIsolatedStorage(string fileName)
        {
            string value = null;
            using (IsolatedStorageFile isoStore = IsolatedStorageFile.GetStore(IsolatedStorageScope.User | IsolatedStorageScope.Assembly, null, null))
            {
                try
                {
                    using (var iStream = new IsolatedStorageFileStream(fileName, FileMode.Open, isoStore))
                    {
                        using (var reader = new StreamReader(iStream))
                        {
                            value = reader.ReadLine();
                        }
                    }
                }
                catch (FileNotFoundException)
                {
                    value = null;
                }
            }

            return value;
        }

        /// <summary>
        /// Writes Key to File
        /// </summary>
        /// <param name="fileName">Name of file where key should be stored</param>
        /// <param name="key">The key which should be stored</param>
        private static void SaveKeyToIsolatedStorage(string fileName, string key)
        {
            if (fileName != null && key != null)
            {
                using (IsolatedStorageFile isoStore = IsolatedStorageFile.GetStore(IsolatedStorageScope.User | IsolatedStorageScope.Assembly, null, null))
                {
                    using (var oStream = new IsolatedStorageFileStream(fileName, FileMode.Create, isoStore))
                    {
                        using (var writer = new StreamWriter(oStream))
                        {
                            writer.WriteLine(key);
                        }
                    }
                }
            }
        }

        /// <summary>
        /// Save Button Click triggers the three entered keys to be saved to their corresponding files.
        /// </summary>
        private void SaveKey_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                SaveKeyToIsolatedStorage(subscriptionKeyFileName, this.SubscriptionKey);
                SaveKeyToIsolatedStorage(endpointIdFileName, this.CustomModelEndpointId);
                MessageBox.Show("Keys are saved to your disk.\nYou do not need to paste it next time.", "Keys");
            }
            catch (Exception exception)
            {
                MessageBox.Show(
                    "Fail to save Keys. Error message: " + exception.Message,
                    "Keys",
                    MessageBoxButton.OK,
                    MessageBoxImage.Error);
            }
        }

        /// <summary>
        /// Checks if keys are valid
        /// </summary>
        private bool AreKeysValid()
        {
            if (this.subscriptionKey == null || this.subscriptionKey.Length <= 0 ||
                ((this.UseCustomModel || this.UseBaseAndCustomModels) && (this.endpointId == null || this.endpointId.Length <= 0)))
            {
                return false;
            }

            return true;
        }

        /// <summary>
        /// Checks if specified audio file exists and returns it
        /// </summary>
        public string GetFile()
        {
            string filePath = "";
            this.Dispatcher.Invoke(() =>
            {
                filePath = this.fileNameTextBox.Text;
            });
            if (!File.Exists(filePath))
            {
                MessageBox.Show("File does not exist!");
                this.WriteLine(this.baseModelLogText, "--- Error : File does not exist! ---");
                this.WriteLine(this.customModelLogText, "--- Error : File does not exist! ---");
                this.EnableButtons();
                return "";
            }
            return filePath;
        }

        /// <summary>
        /// Plays the audio file
        /// </summary>
        private void PlayAudioFile()
        {
            SoundPlayer player = new SoundPlayer(wavFileName);
            player.Load();
            player.Play();
        }

        /// <summary>
        /// Logs the recognition start.
        /// </summary>
        private void LogRecognitionStart(TextBox log, TextBlock currentText)
        {
            string recoSource;
            recoSource = this.UseMicrophone ? "microphone" : "wav file";

            this.SetCurrentText(currentText, string.Empty);
            log.Clear();
            this.WriteLine(log, "\n--- Start speech recognition using " + recoSource + " in " + defaultLocale + " language ----\n\n");
        }

        /// <summary>
        /// Writes the line.
        /// </summary>
        private void WriteLine(TextBox log)
        {
            this.WriteLine(log, string.Empty);
        }

        private void WriteLine(TextBox log, string format, params object[] args)
        {
            var formattedStr = string.Format(CultureInfo.InvariantCulture, format, args);
            Trace.WriteLine(formattedStr);
            this.Dispatcher.Invoke(() =>
            {
                log.AppendText((formattedStr + "\n"));
                log.ScrollToEnd();
            });
        }

        private void SetCurrentText(TextBlock textBlock, string text)
        {
            this.Dispatcher.Invoke(() =>
            {
                textBlock.Text = text;
            });
        }

        /// <summary>
        /// Helper function for INotifyPropertyChanged interface
        /// </summary>
        /// <typeparam name="T">Property type</typeparam>
        /// <param name="caller">Property name</param>
        private void OnPropertyChanged<T>([CallerMemberName]string caller = null)
        {
            var handler = this.PropertyChanged;
            if (handler != null)
            {
                handler(this, new PropertyChangedEventArgs(caller));
            }
        }

        private void RadioButton_Click(object sender, RoutedEventArgs e)
        {
            this.EnableButtons();
        }

        private void SelectFileButton_Click(object sender, RoutedEventArgs e)
        {
            Forms.FileDialog fileDialog = new Forms.OpenFileDialog();
            fileDialog.ShowDialog();
            this.fileNameTextBox.Text = fileDialog.FileName;
        }

        private void HelpButton_Click(object sender, RoutedEventArgs e)
        {
            Process.Start("https://azure.microsoft.com/services/cognitive-services/");
        }

        public static async Task SynthesisToSpeakerAsync()
        {
            // Creates an instance of a speech config with specified subscription key and service region.
            // Replace with your own subscription key and service region (e.g., "westus").
            var config = SpeechConfig.FromSubscription("3b6dce5db367479fb4d679c0c9b853c8", "westus");

            // Creates a speech synthesizer using the default speaker as audio output.
            using (var synthesizer = new SpeechSynthesizer(config))
            {

                string text = "NCU";

                using (var result = await synthesizer.SpeakTextAsync(text))
                {
                    if (result.Reason == ResultReason.SynthesizingAudioCompleted)
                    {
                        
                        Console.WriteLine($"Speech synthesized to speaker for text [{text}]");
                   

                    }
                    else if (result.Reason == ResultReason.Canceled)
                    {
                        var cancellation = SpeechSynthesisCancellationDetails.FromResult(result);
                        Console.WriteLine($"CANCELED: Reason={cancellation.Reason}");

                        if (cancellation.Reason == CancellationReason.Error)
                        {
                            Console.WriteLine($"CANCELED: ErrorCode={cancellation.ErrorCode}");
                            Console.WriteLine($"CANCELED: ErrorDetails=[{cancellation.ErrorDetails}]");
                            Console.WriteLine($"CANCELED: Did you update the subscription info?");
                        }
                    }
                }
            }
        }

        private void EnableButtons()
        {
            this.Dispatcher.Invoke(() =>
            {
                this.startButton.IsEnabled = true;
                this.radioGroup.IsEnabled = true;
                this.optionPanel.IsEnabled = true;
            });
        }
        #endregion

        #region Events
        /// <summary>
        /// Implement INotifyPropertyChanged interface
        /// </summary>
        public event PropertyChangedEventHandler PropertyChanged;

        /// <summary>
        /// Raises the System.Windows.Window.Closed event.
        /// </summary>
        /// <param name="e">An System.EventArgs that contains the event data.</param>
        protected override void OnClosed(EventArgs e)
        {
            base.OnClosed(e);
        }

        #endregion Events



        







    }




}
