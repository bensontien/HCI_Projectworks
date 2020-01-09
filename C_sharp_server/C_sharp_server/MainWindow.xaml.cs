using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;
using System.Net;
using System.Net.Sockets;
using System.Threading;
using System.Diagnostics;

namespace C_sharp_server
{
    /// <summary>
    /// MainWindow.xaml 的互動邏輯
    /// </summary>
    /// 
    
    public partial class MainWindow : Window
    {
        Socket[] SocketsClient;   //支持多人連線
        int SkCIndex;

        //string LocalIP = "192.168.225.2";
        string LocalIP = "140.115.214.114"; // 本機IP
        int SPort = 5050;

        int RDataLen = 256;

        string AcceptS;

        

        public MainWindow()
        {
            InitializeComponent();
        }

        private void Listen()
        {
            //透過resize分配Socket，支援複數連線
            Array.Resize(ref SocketsClient, 1);

            SocketsClient[0] = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);

            SocketsClient[0].Bind(new IPEndPoint(IPAddress.Parse(LocalIP), SPort));

            SocketsClient[0].Listen(10);//允許最多連線10位

            SocketWaitAccept();
        }

        private void SocketWaitAccept()
        {


            bool FlagFinded = false;

            for (int i = 1; i < SocketsClient.Length; i++)

            {

                if (SocketsClient[i] != null)

                {
  
                    if (SocketsClient[i].Connected == false)

                    {

                        SkCIndex = i;

                        FlagFinded = true;

                        break;

                    }

                }

            }

            // FlagFinded 為 false 表示沒有多餘的 Socket 可以給 Client 連線



            if (FlagFinded == false)

            {

                // 增加 Socket 的數目以供下一個 Client 端進行連線

                SkCIndex = SocketsClient.Length;

                Array.Resize(ref SocketsClient, SkCIndex + 1);

            }


            // SckSAcceptProc是接受連線的Process，所以另外提出來寫Multi-thread

            //New一個新的thread:SckSAcceptTd，這個thread start之後才去run SckSAcceptProc

            Thread SckSAcceptTd = new Thread(SocketSAcceptProc);

            SckSAcceptTd.Start();  // 開始執行 SckSAcceptTd 這個執行緒

        }

        private void SocketSAcceptProc()

        {

            // SckSs[0] 若被 Close 的話, SckSs[0].Accept() 會產生錯誤，所以這裡有try

            try

            {

                SocketsClient[SkCIndex] = SocketsClient[0].Accept();  // 等待Client 端連線

                // 能來這表示有 Client 連上線. 記錄該 Client 對應的 SckCIndex

                int Scki = SkCIndex;

                // 再產生另一個執行緒等待下一個 Client 連線

                SocketWaitAccept();


                long IntAcceptData;

                byte[] clientData = new byte[RDataLen];  // 其中RDataLen為每次要接受來自 Client 傳來的資料長度



                while (true)

                {

                    // 等Client 端傳來的資料

                    IntAcceptData = SocketsClient[Scki].Receive(clientData);

                    AcceptS = Encoding.Default.GetString(clientData);
                    
                    if(AcceptS != null)
                    {
                        SocketSend();
                        Debug.Print(AcceptS);
                        AcceptStringText.Text = AcceptS.ToString();
                        AcceptS = null;
                        clientData = null;
                    }

                }

            }

            catch

            {

                //因為是測試，所以就暫時沒寫catch

            }

        }


        private void SocketSend()

        {

            for (int Scki = 1; Scki < SocketsClient.Length; Scki++)

            {



                if (null != SocketsClient[Scki] && SocketsClient[Scki].Connected == true)

                {

                    try

                    {

                        string SendS = "Hello World!\n";      // SendS 在這裡為 string 型態, 為 Server 要傳給 Client 的字串, 測試傳送 "Hello World!" 給Client

                        SocketsClient[Scki].Send(Encoding.ASCII.GetBytes(SendS));

                    }

                    catch

                    {

                        //因為是測試，所以就暫時沒寫catch

                    }

                }

            }

        }

        private void Button_listen_Click(object sender, RoutedEventArgs e)
        {
            Listen();
            AcceptStringText.Text = "Start listen";
        }

        private void TextBox_TextChanged(object sender, TextChangedEventArgs e)
        {
            
        }
    }
}
