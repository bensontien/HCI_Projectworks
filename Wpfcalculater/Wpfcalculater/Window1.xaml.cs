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
using System.Windows.Shapes;
using MySql.Data.MySqlClient;
using System.Data;

namespace Wpfcalculater
{

    /// <summary>
    /// Window1.xaml 的互動邏輯
    /// </summary>
    public partial class Window1 : Window
    {
        
        public Window1()
        {
            InitializeComponent();


            string connString = "server=127.0.0.1;port=3306;user id=root ;password=sheisyuri0405 ; database=cal; Allow User Variables=True;";
            MySqlConnection conn = new MySqlConnection();
            conn.ConnectionString = connString;
            
            string sql = "select * from cal ";

            MySqlCommand cmd = new MySqlCommand(sql, conn);
            
            conn.Open();
            DataTable dt = new DataTable();
            dt.Load(cmd.ExecuteReader());
            
            conn.Close();
            dtgrid.DataContext = dt;
        }

        private void Delete_Click(object sender, RoutedEventArgs e)
        {
            string connection = "server = 127.0.0.1; uid = root; pwd = sheisyuri0405 ; database = cal;";

            using (MySqlConnection conn = new MySqlConnection(connection))
            {
                MySql.Data.MySqlClient.MySqlDataAdapter adapter;
                System.Data.DataSet ds;
                System.Data.DataTable dt;
                adapter = new MySql.Data.MySqlClient.MySqlDataAdapter("select * from cal", conn);
                ds = new System.Data.DataSet();
                ds.Clear();
                adapter.Fill(ds, "cal");
                
                dt = ds.Tables["cal"];
                
                conn.Open();
                int index = dtgrid.SelectedIndex;
                

                string removeVolCred = "DELETE FROM cal WHERE ID = '"+dt.Rows[index]["id"]+"'";

                using (MySqlCommand command = new MySqlCommand(removeVolCred, conn))
                {
                    command.ExecuteNonQuery();
                }

                conn.Close();
            }
        }

        private void Dtgrid_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            
        }
        
    }
}
